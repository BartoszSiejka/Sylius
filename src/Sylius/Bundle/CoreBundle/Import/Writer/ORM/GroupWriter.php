<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Import\Writer\ORM;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManager;

/**
 * Product option writer.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class GroupWriter extends AbstractDoctrineWriter
{
    private $groupRepository;
    
    public function __construct(RepositoryInterface $groupRepository, EntityManager $em)
    {
        parent::__construct($em);
        $this->groupRepository = $groupRepository;
    }
    
    public function process($data) 
    {
        $groupRepository = $this->groupRepository;
        
        if($group = $groupRepository->findOneBy(array('name' => $data['name']))){
            $data['name'] ? $group->setName($data['name']) : $group->getName();
            $data['roles'] ? $group->addRole($data['roles']) : $group->getRoles();
        
            return $group;
        }
        
        $group = $groupRepository->createNew();
        
        $group->setName($data['name']);
        $group->addRole($data['roles']);
        
        return $group;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'import_group';
    }
}