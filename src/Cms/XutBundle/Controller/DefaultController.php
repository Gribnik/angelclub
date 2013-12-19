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
        //$em = $this->getDoctrine()->getManager();

        /** @var ConfigCabinet $config */
        //$config = $this->get('config', $em);
        //$value = $config->getConfig('general/test30424');
        //die(var_dump($value));
        /*$config = new Config();
        $config->setName('test' . rand(0, 99999))
            ->setNode('general/test' . rand(0, 99999))
            ->setValue('testvalue1');
        $em->persist($config);
        $em->flush();*/

        $this->createUserAndRoleTest();


        return $this->render('CmsXutBundle:Default:index.html.twig');
    }

    public function createUserAndRoleTest()
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setUsername('enarc')
            ->setIsActive(true)
            ->setEmail('enarc@atwix.com');

        $factory = $this->get('security.encoder_factory');

        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword('test', $user->getSalt());
        $user->setPassword($password);

        $em->persist($user);
        $em->flush();

    }
}
