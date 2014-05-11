<?php

namespace Cms\XutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        /* Currently we have no admin interface. Go to the home pagex` */
        return $this->redirect($this->generateUrl('cms_xut_homepage'));
    }
}
