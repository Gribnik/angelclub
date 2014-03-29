<?php

namespace Cms\BlogBundle\Controller;
use Cms\XutBundle\Entity\Gist;
use Cms\XutBundle\Entity\Tag;
use Cms\BlogBundle\Form\BlogpostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BlogController extends Controller
{
    public $helper;
    public function __construct()
    {
        //$this->helper = $this->get('backpack');
    }

    public function viewAction($post_id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('CmsXutBundle:Gist')->findOneById($post_id);

        if (!is_null($post)) {
            return $this->render('CmsBlogBundle:Blog:view.html.twig', array(
                'post' => $post
            ));
        } else {
            throw $this->createNotFoundException('message');
        }
    }

    public function listAction($tagname = null)
    {
        // TODO: Reduce queries count, connected to the tags

        $em = $this->getDoctrine()->getManager();
        $blogs = $em->createQueryBuilder()
            ->select('bl')
            ->from('CmsXutBundle:Gist', 'bl')
            ->where('bl.type = :gisttype')
            ->setParameter('gisttype', 'blog')
            ->addOrderBy('bl.date_created');

        if (!is_null($tagname) && !empty($tagname)) {
            $tag =  $em->getRepository('CmsXutBundle:Tag')->findOneByName($tagname);
            if (!is_null($tag)) {
                $blogs = $blogs->innerJoin('bl.tags', 'tg')
                    ->andWhere('tg.id = :tag')
                    ->setParameter('tag', $tag->getId());
            }
        }

        $blogs = $blogs->getQuery()
            ->getResult();

        return $this->render('CmsBlogBundle:Blog:list.html.twig', array(
            'blogs' => $blogs
        ));

    }

    public function editAction($post_id = 0)
    {
        if ($this->_isAdmin()) {
            if (!$post_id) {
                $post = new Gist();
            } else {
                $em = $this->getDoctrine()->getManager();
                $post = $em->getRepository('CmsXutBundle:Gist')->find($post_id);
                /* TODO: check if post exists */
            }

            $form = $this->createForm(new BlogpostType(), $post);

            $view = $this->render('CmsBlogBundle:Blog:post_form.html.twig', array(
                'form' => $form->createView(),
                'post' => $post
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

    public function saveAction($post_id = 0)
    {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST' && $this->_isAdmin()) {
            $em = $this->getDoctrine()->getManager();
            $currentDate = date("Y-m-d H:i:s");
            if (!$post_id) {
                $post = new Gist();
                $post->setType('blog');
                $post->setDateCreated(new \DateTime($currentDate));
            } else {
                $post = $em->getRepository('CmsXutBundle:Gist')->find($post_id);
                if (is_null($post)) {
                    return $this->get('backpack')->sendJsonResponseText('Post with requested id does not exist', 'error');
                }
            }

            $post->setDateUpdated(new \DateTime($currentDate));
            $form = $this->createForm(new BlogpostType(), $post);
            $form->handleRequest($request);
            if ($form->isValid()) {
                /* Tags were passed as string. Process the string */
                $_postValues = $request->request->get('blogpost');
                $this->_setTagsFromString($_postValues['tagsfield'], $post);
                $em->persist($post);
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Changes were saved!'
                );

                if ($post_id > 0) {
                    return $this->redirect($this->generateUrl('blog_post_view', array('post_id' => $post->getId())));
                } else {
                    return $this->redirect($this->generateUrl('blog_index'));
                }
            } else {
                $errors = $form->getErrorsAsString();

                $this->get('session')->getFlashBag()->add(
                    'error',
                    "Cannot save changes " . $errors
                );
                return $this->get('backpack')->sendJsonResponseText('The form has missing required fields', 'error');
            }
        } else { /* TODO: consolidate this into the single unified method */
            $this->get('session')->getFlashBag()->add(
                'error',
                "You don't have permission to complete this action"
            );
            throw new AccessDeniedException();
        }
    }

    public function removeAction($post_id)
    {
        if ($this->_isAdmin()) {
            $em = $this->getDoctrine()->getManager();
            $post = $em->getRepository('CmsXutBundle:Gist')->find($post_id);

            if (!is_null($post)) {
                $em->remove($post);
                $em->flush();
            } else {
                return $this->get('backpack')->sendJsonResponseText('Post with requested id does not exist', 'error');
            }

            return $this->get('backpack')->sendJsonResponseText('');
        } else {
            throw new AccessDeniedException();
        }
    }

    /**
     * Uploads image from WYSISYG editor
     *
     * @return mixed
     */
    public function uploadimageAction()
    {
        $files = $this->getRequest()->files->all();
        $uploadedFile = current($files);

        $allowedTypes = array('image/jpeg', 'image/jpg', 'image/pjpeg', 'image/x-png', 'image/png');
        if (in_array($uploadedFile->getMimeType(), $allowedTypes)) {
            try {
                $gist = new Gist();
                $gist->setFile($uploadedFile);
                $newFile = $gist->upload(); // TODO: Separate uploaded images gallery|blog|banner etc
                $response = array(
                    'status' => 'success',
                    'link'   => $gist->getUploadDir() . $newFile
                );
            } catch (Exception $e) {
                $response = array(
                    'status' => 'error'
                );
            }

        } else {
            $response = array(
                'status'  => 'error',
                'code' => 'FILETYPE_NOT_ALLOWED'
            );
        }

        return $this->get('backpack')->sendJsonResponse($response);
    }

    protected function _isAdmin()
    {
        return true === $this->get('security.context')->isGranted('ROLE_ADMIN');
    }

    /**
     * Process tags string
     *
     * @param string $tags
     * @param \Cms\XutBundle\Entity\Gist $post
     */
    protected function _setTagsFromString($tags, $post)
    {
        // TODO: explode this method into the smallest parts

        $newTags = array();
        if (!empty($tags)) {
            $em = $this->getDoctrine()->getManager();
            $tagsArray = explode(',', $tags);
            array_walk($tagsArray, array($this, '_normalizeTagNames'));

            /* Get previous gist tags */
            $oldTags = $post->getTags();
            $oldTagsArray = array();
            foreach ($oldTags as $_oldTag) {
                array_push($oldTagsArray, $_oldTag->getName());
                /* Remove the old tags from the post if necessary */
                if (!in_array($_oldTag->getName(), $tagsArray)) {
                    $post->removeTag($_oldTag);
                    // TODO: make an observer to check if removed tags are still being used somewhere
                }
            }

            /* If there are only old tags - no reason to proceed */
            if (count(array_diff($tagsArray, $oldTagsArray)) < 1) {
                return;
            }

            /* Get all tags */
            $allTags = $em->getRepository('CmsXutBundle:Tag')->findAll();

            /* Add tags to the post */
            foreach ($allTags as $_tag) {
                if (count($tagsArray) < 1) {
                    break;
                }
                if (false !== ($key = array_search($_tag->getName(), $tagsArray))) {
                    if (!in_array($_tag->getName(), $oldTagsArray)) {
                        array_push($newTags, $_tag->getId());
                        $post->addTag($_tag);
                    }
                    unset($tagsArray[$key]);
                }
            }

            /* We have new tags, add them to the database */
            if (count($tagsArray) > 0) {
                foreach ($tagsArray as $_tag) {
                    $tag = new Tag();
                    $tag->setType('blog');
                    $tag->setName($_tag);
                    $em->persist($tag);
                    $em->flush();
                    $post->addTag($tag); // TODO: might be optimized a bit
                }

            }
        } else {
            /* If the tags string is empty, remove all tags */
            $post->removeAllTags();
        }
    }

    protected function _normalizeTagNames(&$tag)
    {
        // TODO: make ability to have not only lovercased tags
        if (empty($tag)) {
            $tag = null;
            return;
        }
        $tag = trim($tag);
        $tag = strtolower($tag);
    }
}