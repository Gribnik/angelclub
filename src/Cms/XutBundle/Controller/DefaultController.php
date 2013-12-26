<?php

namespace Cms\XutBundle\Controller;

use Cms\XutBundle\DependencyInjection\ConfigCabinet;
use Cms\XutBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cms\XutBundle\Entity\Config;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CmsXutBundle:Default:index.html.twig');
    }

    public function createUserAndRoleTest()
    {
        $em = $this->getDoctrine()->getManager();
        /*$user = new User();
        $user->setUsername('enarc')
            ->setIsActive(true)
            ->setEmail('enarc@atwix.com');

        $factory = $this->get('security.encoder_factory');

        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword('test', $user->getSalt());
        $user->setPassword($password);

        $em->persist($user);
        $em->flush();*/



    }
}
