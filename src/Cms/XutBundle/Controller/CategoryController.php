<?php

namespace Cms\XutBundle\Controller;

use Cms\XutBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Cms\XutBundle\Form\CategoryType;

class CategoryController extends Controller
{
    public function editAction($category_id = 0)
    {
        if ($this->_isAdmin()) {
            if (!$category_id) {
                $category = new Category();
            } else {
                $em = $this->getDoctrine()->getManager();
                $category = $em->getRepository('CmsXutBundle:Gist')->find($category_id);
                /* TODO: check if category exists */
            }

            $form = $this->createForm(new CategoryType(), $category);

            return $this->render('CmsXutBundle:Admin:category_edit.html.twig', array(
                'form' => $form->createView(),
                'category' => $category
            ));
        } else {
            throw new AccessDeniedException();
        }
    }

    public function saveAction($category_id = 0)
    {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST' && $this->_isAdmin()) {
            $em = $this->getDoctrine()->getManager();
            if (!$category_id) {
                $category = new Category();
                $category->setType('blog');
            } else {
                $category = $em->getRepository('CmsXutBundle:Category')->find($category_id);
                if (is_null($category)) {
                    return $this->get('backpack')->sendJsonResponse('Category with requested id does not exist', 'error');
                }
            }

            $form = $this->createForm(new CategoryType(), $category);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($category);
                $em->flush();
            } else {
                return $this->get('backpack')->sendJsonResponse('The form has missing required fields', 'error');
            }
        } else {
            throw new AccessDeniedException();
        }

        return $this->get('backpack')->sendJsonResponse('');
    }

    public function removeAction($category_id)
    {
        if ($this->_isAdmin()) {
            $em = $this->getDoctrine()->getManager();
            $category = $em->getRepository('CmsXutBundle:Category')->find($category_id);

            if (!is_null($category)) {
                $em->remove($category);
                $em->flush();
            } else {
                return $this->get('backpack')->sendJsonResponse('Category with requested id does not exist', 'error');
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
}