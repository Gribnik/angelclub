<?php

namespace Cms\XutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CmsXutBundle:Default:index.html.twig');
    }
}
