<?php

namespace Cms\XutBundle\Controller;

use Cms\XutBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Cms\XutBundle\Form\CategoryType;

class CategoryController extends Controller
{
    public function listAction($type)
    {
        $em = $this->getDoctrine()->getManager();
        $this->get('backpack')->setEntityManager($em);
        $categories = $this->get('backpack')->getCategoriesList($type);

        return $this->render('CmsXutBundle:Admin:categories_list.html.twig', array(
            'categories' => $categories
        ));
    }


    public function editAction()
    {
        if ($this->_isAdmin()) {
            $em = $this->getDoctrine()->getManager();
            $this->get('backpack')->setEntityManager($em);
            $categories = $this->get('backpack')->getCategoriesList('gallery');
            $forms = array();

            if (count($categories) > 0) {
                foreach ($categories as $_category) {
                    $forms[] = array(
                        'formview' => $this->createForm(new CategoryType(), $_category, array('method' => 'PUT'))->createView(),
                        'category' => $_category
                    );
                }
            }

            $formNew = $this->createForm(new CategoryType(), new Category());

            return $this->render('CmsXutBundle:Admin:categories_list.html.twig', array(
                'forms'     => $forms,
                'form_new'  => $formNew->createView()
            ));

        } else {
            throw new AccessDeniedException();
        }
    }

    public function saveAction($category_id = 0)
    {
        $putJson = file_get_contents('php://input');
        $parameters = (array) json_decode($putJson);

        /* There are some problems using form->put->request flow with backbone. So, we have some dog-nails here */
        
        $necessaryParameters = array('name' => '', '_token' => '');
        $parameters = array_intersect_key($parameters, $necessaryParameters);
        $request = $this->getRequest();
        if ($request->getMethod() == 'PUT' && $this->_isAdmin()) {
            $requestToHandle = $request->create($request->getUri(), 'PUT',
                array('category' => (array) $parameters));
            $em = $this->getDoctrine()->getManager();
            if (!$category_id) {
                $category = new Category();
                $category->setType('blog');
            } else {
                $category = $em->getRepository('CmsXutBundle:Category')->find($category_id);
                if (is_null($category)) {
                    return $this->get('backpack')->sendJsonResponseText('Category with requested id does not exist', 'error');
                }
            }

            $form = $this->createForm(new CategoryType(), $category, array('method' => 'PUT'));
            $form->handleRequest($requestToHandle);
            if ($form->isValid()) {
                $em->persist($category);
                $em->flush();
            } else {
                return $this->get('backpack')->sendJsonResponseText('The form has missing required fields' . $form->getErrorsAsString(),
                    'error');
            }
        } else {
            throw new AccessDeniedException();
        }

        return $this->get('backpack')->sendJsonResponseText('');
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
                return $this->get('backpack')->sendJsonResponseText('Category with requested id does not exist', 'error');
            }

            return $this->get('backpack')->sendJsonResponseText('');
        } else {
            throw new AccessDeniedException();
        }
    }

    protected function _isAdmin()
    {
        return true === $this->get('security.context')->isGranted('ROLE_ADMIN');
    }
}