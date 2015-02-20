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
class ProductAttributeReader extends AbstractDoctrineReader
{
    private $productAttributeRepository;
    
    public function __construct(EntityRepository $productAttributeRepository, ArrayIteratorFactoryInterface $iteratorFactory)
    {
        parent::__construct($iteratorFactory);
        $this->productAttributeRepository = $productAttributeRepository;
    }
    
    public function getQuery()
    {
        $query = $this->productAttributeRepository->createQueryBuilder('pa')
            ->getQuery();
        
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'product_attribute';
    }
    
    protected function process($attribute)
    {
        $attributes = array();
        $createdAt = (string) $attribute->getCreatedAt()->format('Y-m-d H:m:s');
        
        $attributes = array_merge($attributes, array(
            'id'            => $attribute->getId(),
            'name'          => $attribute->getName(),
            'type'          => $attribute->getType(),
            'created_at'    => $createdAt,
            'presentation'  => $attribute->getPresentation(),
        ));
        
        return $attributes;
    }
}