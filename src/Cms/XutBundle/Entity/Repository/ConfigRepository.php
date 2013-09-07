<?php
namespace Cms\XutBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ConfigRepository extends EntityRepository
{
    public function loadConfigByNode($node)
    {
        $db = $this->createQueryBuilder('bl')
            ->select("bl.title_id, bl.node, bl.name, bl.value");

        if (!empty($node)) {
            $db->where("bl.node IN (:node)")
                ->setParameter('node', $node);
        }

        $result = $db->getQuery()->getArrayResult();
        $configSet = array();
        foreach ($result as $_configRow) {
            $configSet[$_configRow['node']] = $_configRow;
        }
        return $configSet;
    }
}