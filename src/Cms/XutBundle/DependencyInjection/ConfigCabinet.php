<?php

namespace Cms\XutBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Cms\XutBundle\Entity\Config;
use Cms\XutBundle\Entity\Repository\ConfigRepository;
use Doctrine\ORM\EntityManager;

class ConfigCabinet
{
    protected $_config = array();
    protected $_loadNodes = array('global'); /* TODO: move to config */

    /** @var \Doctrine\ORM\EntityManager  */
    protected $_entityManager;

    public function __construct(EntityManager $entityManager) {
        $this->_entityManager = $entityManager;
    }

    protected function _getConfigSet()
    {
        if (1 > count($this->_config)) {
            $configCollection = $this->_entityManager->getRepository('CmsXutBundle:Config')
                ->findAll();
            foreach ($configCollection as $_configRecord) {
                $configPath = explode('/', $_configRecord->getNode());
                $this->_config[$configPath[0]][$configPath[1]] = $_configRecord;
            }
        }

        return $this->_config;
    }

    public function getConfig($xpath)
    {
        $configPath = explode('/', $xpath);
        if (count($configPath) == 2) { // The path will be something like "hello/world"
            $config = $this->_getConfigSet();
            if (isset($config[$configPath[0]][$configPath[1]])) {
                return $config[$configPath[0]][$configPath[1]];
            }
        } else {
            throw new \ErrorException("Wrong config path specified");
        }

        return NULL;
    }

}