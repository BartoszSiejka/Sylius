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
 * Export product attribute reader.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductArchetypeReader extends AbstractDoctrineReader
{
    private $productArchetypeRepository;
    
    public function __construct(EntityRepository $productArchetypeRepository, ArrayIteratorFactoryInterface $iteratorFactory)
    {
        parent::__construct($iteratorFactory);
        $this->productArchetypeRepository = $productArchetypeRepository;
    }
    
    public function getQuery()
    {
        $query = $this->productArchetypeRepository->createQueryBuilder('pac')
            ->getQuery();
        
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'product_archetype';
    }
    
    protected function process($archetype)
    {
        $archetypes = array();
        $options = $archetype->getOptions()->toArray();
        $attributes = $archetype->getAttributes()->toArray();
        $createdAt = (string) $archetype->getCreatedAt()->format('Y-m-d H:m:s');
        $parent = $archetype->getParent();
        
        $attributeName = implode("~", $attributes);
        $optionName = implode("~", $options);
        
        $archetypes = array_merge($archetypes, array(
            'id'         => $archetype->getId(),
            'code'       => $archetype->getCode(),
            'name'       => $archetype->getName(),
            'parent'     => $parent->getName(),
            'options'    => $optionName,
            'attributes' => $attributeName,
            'created_at' => $createdAt,
        ));
         
         return $archetypes;
    }
}