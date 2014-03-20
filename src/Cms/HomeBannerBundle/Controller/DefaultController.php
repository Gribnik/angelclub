<?php

namespace Cms\HomeBannerBundle\Controller;

use Cms\HomeBannerBundle\Entity\Homebanner;
use Cms\HomeBannerBundle\Form\HomebannerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    /**
     * Saves the home banner data
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function setImageAction(Request $request)
    {
        if ($this->_isAdmin()) {
            $homebanner = $this->_getInitialBanner();
            $form = $this->createForm(new HomebannerType(), $homebanner);

            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                /* TODO: make max upload size */
                /* TODO: Date Created and Date Updated */
                $homebanner->upload();

                $em->persist($homebanner);
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Changes were saved!'
                );

                return $this->redirect($this->generateUrl('cms_xut_homepage'));
            } else {
                $errors = $form->getErrorsAsString();
                $this->get('session')->getFlashBag()->add(
                    'error',
                    "Cannot save changes " . $errors
                );
                return $this->redirect($this->generateUrl('cms_xut_homepage'));
            }
        } else { /* TODO: consolidate this into the single unified method */
            $this->get('session')->getFlashBag()->add(
                'error',
                "You don't have permission to complete this action"
            );
            throw new AccessDeniedException();
        }
    }

    /**
     * Returns home banner edit form content
     *
     * @return mixed
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function getFormAction()
    {
        if ($this->_isAdmin()) {
            $homebanner = $this->_getInitialBanner();
            $form = $this->createForm(new HomebannerType(), $homebanner);
            $json = array();
            $view = $this->render('CmsHomeBannerBundle:Default:form.html.twig', array(
                'form' => $form->createView()
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

    /**
     * Returns home banner content
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getImageAction()
    {
        $homebanner = $this->_getInitialBanner();
        return $this->render('CmsHomeBannerBundle:Default:banner.html.twig', array(
            'banner' => $homebanner
        ));
    }

    /**
     * Returns first banner. Temporary solution
     *
     * @return Homebanner
     */
    protected function _getInitialBanner()
    {
        $em = $this->getDoctrine()->getManager();
        $banner = $em->getRepository('CmsHomeBannerBundle:Homebanner')->findOneById(1);
        $currentDate = date("Y-m-d H:i:s");
        if (is_null($banner)) {
            $banner = new Homebanner();
            $banner->setDateCreated(new \DateTime($currentDate));
        }
        $banner->setDateUpdated(new \DateTime($currentDate));
        return $banner;
    }

    /**
     * Check if current user has admin role
     *
     * @return bool
     */
    protected function _isAdmin()
    {
        return true === $this->get('security.context')->isGranted('ROLE_ADMIN');
    }
}
