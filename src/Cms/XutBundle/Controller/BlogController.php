<?php

namespace Cms\XutBundle\Controller;
use Cms\XutBundle\Entity\Gist;
use Cms\XutBundle\Entity\Tag;
use Cms\XutBundle\Form\BlogpostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            return $this->render('CmsXutBundle:Blog:view.html.twig', array(
                'post' => $post
            ));
        } else {
            throw $this->createNotFoundException('message');
        }
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $blogs = $em->createQueryBuilder()
            ->select('bl')
            ->from('CmsXutBundle:Gist', 'bl')
            ->where('bl.type = :blogtype')
            ->setParameter('blogtype', 'blog')
            ->addOrderBy('bl.date_created')
            ->getQuery()
            ->getResult();

        return $this->render('CmsXutBundle:Blog:list.html.twig', array(
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

            return $this->render('CmsXutBundle:Blog:post_form.html.twig', array(
                'form' => $form->createView(),
                'post' => $post
            ));
        } else {
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
                    return $this->get('backpack')->sendJsonResponse('Post with requested id does not exist', 'error');
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
            } else {
                return $this->get('backpack')->sendJsonResponse('The form has missing required fields', 'error');
            }
        } else {
            throw new AccessDeniedException();
        }

        return $this->get('backpack')->sendJsonResponse('');
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
                return $this->get('backpack')->sendJsonResponse('Post with requested id does not exist', 'error');
            }

            return $this->get('backpack')->sendJsonResponse('');
        } else {
            throw new AccessDeniedException();
        }
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
        $newTags = array();
        if (!empty($tags)) {
            $em = $this->getDoctrine()->getManager();
            $tagsArray = explode(',', $tags);
            array_walk($tagsArray, array($this, '_normalizeTagNames'));

            /* Get all tags */
            $allTags = $em->getRepository('CmsXutBundle:Tag')->findAll();

            /* Add tags to the post */
            foreach ($allTags as $_tag) {
                if (count($tagsArray) < 1) {
                    break;
                }
                if (false !== ($key = array_search($_tag->getName(), $tagsArray))) {
                    array_push($newTags, $_tag->getId());
                    $post->addTag($_tag);
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
                }
                $em->flush();
            }
        } else {
            /* If the tags string is empty, remove all tags */
            $post->removeAllTags(); /* FIXME: */
        }
    }

    protected function _normalizeTagNames(&$tag)
    {
        $tag = trim($tag);
    }
}