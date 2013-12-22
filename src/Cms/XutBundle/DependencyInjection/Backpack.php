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
    private $_contentFilters;

    public function __construct() {
        $this->_session = new Session();
        $this->_session->start();
    }

   public function filterContent($content)
   {
       return $content;
   }
}