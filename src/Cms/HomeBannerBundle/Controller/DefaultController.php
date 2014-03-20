<?php

namespace Cms\HomeBannerBundle\Controller;

use Cms\HomeBannerBundle\Entity\Homebanner;
use Cms\HomeBannerBundle\Form\HomebannerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function setImageAction(Request $request)
    {
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
    }

    public function getFormAction()
    {
        /* TODO: secure this area */
        $homebanner = $this->_getInitialBanner();
        $form = $this->createForm(new HomebannerType(), $homebanner);
        $json = array();
        $view = $this->render('CmsHomeBannerBundle:Default:form.html.twig', array(
            'form' => $form->createView()
        ));
        $json['content'] = $view->getContent();

        return $this->get('backpack')->sendJsonResponse($json);
    }

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
}
