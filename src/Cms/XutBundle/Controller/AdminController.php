<?php

namespace Cms\XutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {

        return $this->render('CmsXutBundle:Admin:index.html.twig');
    }
}
