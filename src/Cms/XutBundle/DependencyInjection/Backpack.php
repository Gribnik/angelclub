<?php

namespace Cms\XutBundle\DependencyInjection;

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

    /*public function __construct() {
        $this->_session = new Session();
        $this->_session->start();
    }*/

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


}