<?php

namespace Cms\XutBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Operate with different session data
 *
 * Class Backpack
 * @package Cms\XutBundle\DependencyInjection
 */
class Backpack
{
    private $_session;
    private $_contentFilters;

    /** @var \Doctrine\ORM\EntityManager  */
    protected $_entityManager;

    public function sendJsonResponseText($text, $status='success')
    {
        $response = new Response();
        $jsonContent = array(
            'text'   => $text,
            'status' => $status
        );
        $response->setContent(json_encode($jsonContent));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function sendJsonResponse(array $data)
    {
        $response = new Response();
        $jsonContent = json_encode($data);
        $response->setContent($jsonContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function setEntityManager($em)
    {
        $this->_entityManager = $em;
    }

    public function getEntityManager()
    {
        if (null === $this->_entityManager) {
            throw new Exception('The entity manager should be defined first');
        } else {
            return $this->_entityManager;
        }
    }

    /**
     * Returns categories filtered by the requested type
     *
     * @param string $type
     * @return mixed
     */
    public function getCategoriesList($type)
    {
        $categories = $this->getEntityManager()->getRepository('CmsXutBundle:Category')->findByType($type);

        return $categories;
    }


}