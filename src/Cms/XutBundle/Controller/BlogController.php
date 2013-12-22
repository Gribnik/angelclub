<?php

namespace Cms\XutBundle\Controller;
use Cms\XutBundle\Entity\Gist;
use Cms\XutBundle\Form\BlogpostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    public function viewAction()
    {

    }

    public function editAction($post_id = 0)
    {
        if (!$post_id) {
            $post = new Gist();
        } else {
            $em = $this->getDoctrine()->getManager();
            $post = $em->getRepository('CmsXutBundle:Gist')->find($post_id);
        }

        $form = $this->createForm(new BlogpostType(), $post);

        return $this->render('CmsXutBundle:Blog:post_form.html.twig', array(
            'form' => $form->createView(),
            'post' => $post
        ));
    }

    public function saveAction($post_id = 0)
    {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') { //TODO: Add admin role check
            $em = $this->getDoctrine()->getManager();
            $currentDate = date("Y-m-d H:i:s");
            if (!$post_id) {
                $post = new Gist();
                $post->setType('blog');
                $post->setDateCreated(new \DateTime($currentDate));
            } else {
                $post = $em->getRepository('CmsXutBundle:Gist')->find($post_id);
                // TODO: Make sure that post exists
            }

            $post->setDateUpdated(new \DateTime($currentDate));
            $form = $this->createForm(new BlogpostType(), $post);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($post);
                $em->flush();

                return 'done';
            }
        }
    }

    public function removeAction()
    {

    }
}