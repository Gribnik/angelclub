<?php

namespace Cms\GalleryBundle\Controller;

use Cms\XutBundle\Entity\Gist;
use Cms\XutBundle\Entity\Tag;
use Cms\GalleryBundle\Form\ImageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->listAction();
    }

    /**
     * Shows requested gallery image
     *
     * @param int $image_id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function viewAction($image_id)
    {
        $em = $this->getDoctrine()->getManager();
        $image = $em->getRepository('CmsXutBundle:Gist')->findOneById($image_id);

        if (!is_null($image)) {
            return $this->render('CmsGalleryBundle:Default:view.html.twig', array(
                'image' => $image
            ));
        } else {
            throw $this->createNotFoundException('Requested item does not exist');
        }
    }

    /**
     * Lists all galery images
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction($tagname = null)
    {
        // TODO: Reduce queries count, connected to the tags

        $em = $this->getDoctrine()->getManager();
        $images = $em->createQueryBuilder()
            ->select('bl')
            ->from('CmsXutBundle:Gist', 'bl')
            ->where('bl.type = :gisttype')
            ->setParameter('gisttype', 'image')
            ->addOrderBy('bl.date_created');

        if (!is_null($tagname) && !empty($tagname)) {
            $tag =  $em->getRepository('CmsXutBundle:Tag')->findOneByName($tagname);
            if (!is_null($tag)) {
                $images = $images->innerJoin('bl.tags', 'tg')
                    ->andWhere('tg.id = :tag')
                    ->setParameter('tag', $tag->getId());
            }
        }

        $images = $images->getQuery()
            ->getResult();

        return $this->render('CmsGalleryBundle:Default:list.html.twig', array(
            'images' => $images
        ));
    }

    /**
     * Renders edit image page
     *
     * @param int $image_id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function editAction($image_id = 0)
    {
        if ($this->_isAdmin()) {
            if (!$image_id) {
                $image = new Gist();
            } else {
                $em = $this->getDoctrine()->getManager();
                $image = $em->getRepository('CmsXutBundle:Gist')->find($image_id);
                /* TODO: check if image exists */
            }

            $form = $this->createForm(new ImageType(), $image);

            $view =  $this->render('CmsGalleryBundle:Default:form_multi.html.twig', array(
                'form' => $form->createView(),
                'image' => $image
            ));
            $json['content'] = $view->getContent();

            return $this->get('backpack')->sendJsonResponse($json);
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                "You don't have permission to complete this action"
            );
            throw new AccessDeniedException();
        }
    }

    public function saveAction($image_id = 0)
    {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST' && $this->_isAdmin()) {
            $em = $this->getDoctrine()->getManager();
            $currentDate = date("Y-m-d H:i:s");
            if (!$image_id) {
                $image = new Gist();
                $image->setType('image');
                $image->setDateCreated(new \DateTime($currentDate));
            } else {
                $image = $em->getRepository('CmsXutBundle:Gist')->find($image_id);
                if (is_null($image)) {
                    return $this->get('backpack')->sendJsonResponseText('Image with requested id does not exist', 'error');
                }
            }

            // TODO: Remove previous image if the new one has been uploaded

            $image->setDateUpdated(new \DateTime($currentDate));
            $form = $this->createForm(new ImageType(), $image);
            $form->handleRequest($request);
            if ($form->isValid()) {
                /* Tags were passed as string. Process the string */
                $_postValues = $request->request->get('gallery_image'); //FIXME: another form name
                $this->_setTagsFromString($_postValues['tagsfield'], $image);
                $image->upload();
                $em->persist($image);
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Changes were saved!'
                );

                return $this->redirect($this->generateUrl('gallery_index'));

            } else {
                $errors = $form->getErrorsAsString();
                $this->get('session')->getFlashBag()->add(
                    'error',
                    "Cannot save changes " . $errors
                );

                return $this->get('backpack')->sendJsonResponseText('The form has missing required fields', 'error');
            }
        } else {
            throw new AccessDeniedException();
        }
    }

    public function uploadAction()
    {
        $em = $this->getDoctrine()->getManager();
        $files = $this->getRequest()->files->all();
        $json = array();
        if (count($files) > 0) {
            $currentDate = date("Y-m-d H:i:s");
            foreach ($files as $_file) {
                $image = new Gist();
                $image->setFile(current($_file));
                $newFile = $image->upload();
                $image->setType('image')
                    ->setName($newFile)
                    ->setDateCreated(new \DateTime($currentDate))
                    ->setDateUpdated(new \DateTime($currentDate));
                $em->persist($image);
            }
            $em->flush();
        }


        $form = $this->createForm(new ImageType(), $image);
        $view =  $this->render('CmsGalleryBundle:Default:response_form.html.twig', array(
            'form'      => $form->createView(),
            'image'     => $image,
        ));

        $json['imageForm'] = $view->getContent();


        return $this->get('backpack')->sendJsonResponse($json);
    }


    public function massEditAction()
    {
        $params = $this->getRequest()->request->all();
        if (isset($params['imagesDetails'])) {
            $em = $this->getDoctrine()->getManager();
            foreach ($params['imagesDetails'] as $_imageDetails) {
                $details = array();
                parse_str($_imageDetails, $details);
                if ($details['image_id'] > 0) {
                    $image = $em->getRepository('CmsXutBundle:Gist')->find($details['image_id'] );
                    if (NULL !== $image) {
                        $haschanges = false;
                        if (empty($details['is_removed'])) {
                            if (!empty($details['content'])) {
                                $image->setContent($details['content']);
                                $haschanges = true;
                            }
                            if (!empty($details['tagsfield'])) {
                                $this->_setTagsFromString($details['tagsfield'], $image);
                                $haschanges = true;
                            }

                            if (true === $haschanges) {
                                $em->persist($image);
                            }

                        } else {
                            $em->remove($image);
                        }
                    }
                }
            }

            $em->flush();

        }

        $json['status'] = 'success';
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Changes were saved!'
        );

        return $this->get('backpack')->sendJsonResponse($json);
    }


    protected function _setTagsFromString($tags, $image)
    {
        // TODO: explode this method into the smallest parts

        $newTags = array();
        if (!empty($tags)) {
            $em = $this->getDoctrine()->getManager();
            $tagsArray = explode(',', $tags);
            array_walk($tagsArray, array($this, '_normalizeTagNames'));

            /* Get previous gist tags */
            $oldTags = $image->getTags();
            $oldTagsArray = array();
            foreach ($oldTags as $_oldTag) {
                array_push($oldTagsArray, $_oldTag->getName());
                /* Remove the old tags from the post if necessary */
                if (!in_array($_oldTag->getName(), $tagsArray)) {
                    $image->removeTag($_oldTag);
                    // TODO: make an observer to check if removed tags are still being used somewhere
                }
            }

            /* If there are only old tags - no reason to proceed */
            if (count(array_diff($tagsArray, $oldTagsArray)) < 1) {
                return;
            }

            /* Get all tags */
            $allTags = $em->getRepository('CmsXutBundle:Tag')->findAll();

            // TODO: get all tags only by type */

            /* Add tags to the post */
            foreach ($allTags as $_tag) {
                if (count($tagsArray) < 1) {
                    break;
                }
                if (false !== ($key = array_search($_tag->getName(), $tagsArray))) {
                    if (!in_array($_tag->getName(), $oldTagsArray)) {
                        array_push($newTags, $_tag->getId());
                        $image->addTag($_tag);
                    }
                    unset($tagsArray[$key]);
                }
            }

            /* We have new tags, add them to the database */
            if (count($tagsArray) > 0) {
                foreach ($tagsArray as $_tag) {
                    $tag = new Tag();
                    $tag->setType('image');
                    $tag->setName($_tag);
                    $em->persist($tag);
                    $em->flush();
                    $image->addTag($tag); // TODO: might be optimized a bit
                }

            }
        } else {
            /* If the tags string is empty, remove all tags */
            $image->removeAllTags();
        }
    }


    public function removeAction($image_id)
    {
        if ($this->_isAdmin()) {
            $em = $this->getDoctrine()->getManager();
            $image = $em->getRepository('CmsXutBundle:Gist')->find($image_id);

            if (!is_null($image)) {
                $em->remove($image);
                $em->flush();
            } else {
                return $this->get('backpack')->sendJsonResponseText('Image with requested id does not exist', 'error');
            }

            return $this->get('backpack')->sendJsonResponseText('');
        } else {
            throw new AccessDeniedException();
        }
    }

    /**
     * Returns true if current user has been authorized as admin
     *
     * @return bool
     */
    protected function _isAdmin()
    {
        return true === $this->get('security.context')->isGranted('ROLE_ADMIN');
    }

    protected function _normalizeTagNames(&$tag)
    {
        $tag = trim($tag);
    }
}
