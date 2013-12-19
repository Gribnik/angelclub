<?php

namespace Cms\XutBundle\DependencyInjection;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Operate with different session data
 *
 * Class Backpack
 * @package Cms\XutBundle\DependencyInjection
 */
class Backpack
{
    private $_session;

    public function __construct() {
        $this->_session = new Session();
        $this->_session->start();
    }

    public function createToken($lifetime)
    {

        /** TODO: use mcrypt to generate tokens */
        $token = $this->generateToken();
        if (!empty($token)) {
            $this->_session->set('auth_token', $token);
            $response = new Response();
            $cookie = new Cookie('auth_token', $token, $lifetime);
            $response->headers->setCookie($cookie);
        } else {
            throw new \ErrorException("Unable to generate token");
        }
    }

    public function verifyToken($request)
    {
        $token = $this->_session->get('auth_token');
        if (!empty($token)) {
            $cookieToken = $request->cookies->get('auth_token');
            if ($cookieToken == $token) {
                return true;
            }
        }

        return false;
    }

    public function generateToken()
    {
        $time = time();
        $salt = rand(0, 2227);
        $customerIp = $_SERVER['REMOTE_ADDR'];
        $token = sha1($time . $salt . $customerIp);

        return $token;
    }
}