<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Export\Reader\ORM;

use Doctrine\ORM\EntityRepository;
use Sylius\Component\ImportExport\Factory\ArrayIteratorFactoryInterface;

/**
 * Export product option reader.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class GroupReader extends AbstractDoctrineReader
{
    private $groupRepository;
    
    public function __construct(EntityRepository $groupRepository, ArrayIteratorFactoryInterface $iteratorFactory)
    {
        parent::__construct($iteratorFactory);
        $this->groupRepository = $groupRepository;
    }
    
    public function process($group)
    {
        $groupArray = array();
        $roles = implode("~", $group->getRoles());
        
        $groupArray = array_merge($groupArray, array(
            'id'    => $group->getId(),
            'name'  => $group->getName(),
            'roles' => $roles   
        ));
        
        return $groupArray;
    }
    
    public function getQuery()
    {
        $query = $this->groupRepository->createQueryBuilder('g')
            ->getQuery();
        
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'group';
    }
}