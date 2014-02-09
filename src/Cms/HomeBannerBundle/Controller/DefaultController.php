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
        //if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /* TODO: make max upload size */
            /* TODO: Date Created and Date Updated */
            $homebanner->upload();

            $em->persist($homebanner);
            $em->flush();

            return $this->get('backpack')->sendJsonResponse('The image has been saved successfully');
       // } else {
       //     $errors = $form->getErrors();
       //     return $this->get('backpack')->sendJsonResponse('There was an en error during image saving', 'error');
       // }
    }

    public function getFormAction()
    {
        /* TODO: secure this area */
        $homebanner = $this->_getInitialBanner();
        $form = $this->createForm(new HomebannerType(), $homebanner);
        return $this->render('CmsHomeBannerBundle:Default:form.html.twig', array(
            'form' => $form->createView()
        ));
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
