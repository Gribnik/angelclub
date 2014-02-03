<?php

namespace Cms\HomeBannerBundle\Controller;

use Cms\HomeBannerBundle\Entity\Homebanner;
use Cms\HomeBannerBundle\Form\HomebannerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function setimageAction()
    {
        return $this->get('backpack')->sendJsonResponse('The image has been successfully changed');
    }

    public function getformAction()
    {
        /* TODO: secure this area */
        $homebanner = new Homebanner();
        $form = $this->createForm(new HomebannerType(), $homebanner);

        return $this->render('CmsHomeBannerBundle:Default:form.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
