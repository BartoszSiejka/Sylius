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

use Doctrine\ORM\EntityManager;
use Sylius\Component\Product\Model\Product;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Product writer.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductWriter extends AbstractDoctrineWriter
{
    /** 
     * @var RepositoryInterface
     */
    private $productRepository;
    /** 
     * @var RepositoryInterface
     */
    private $archetypeRepository;
    /** 
     * @var RepositoryInterface
     */
    private $taxCategoryRepository;
    /** 
     * @var RepositoryInterface
     */
    private $shippingCategoryRepository;
        
    public function __construct(
        RepositoryInterface $productRepository,
        RepositoryInterface $archetypeRepository,
        RepositoryInterface $taxCategoryRepository,
        RepositoryInterface $shippingCategoryRepository, 
        EntityManager $em)
    {
        parent::__construct($em);
        $this->productRepository = $productRepository;
        $this->archetypeRepository = $archetypeRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;
        $this->shippingCategoryRepository = $shippingCategoryRepository;
    }
    
    public function process($data) 
    {
        $product = $this->productRepository->find($data['id']);

        if (null === $product) {
            $product = $this->productRepository->createNew();
        }

        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }
        if (isset($data['description'])) {
            $product->setDescription($data['description']);
        }
        if (isset($data['short_description'])) {
            $product->setShortDescription($data['short_description']);
        }
        if (!empty($data['archetype'])) {
            $archetype = $this->archetypeRepository->findOneBy(array('code' => $data['archetype']));
            $product->setArchetype($archetype);
        }
        if (!empty($data['tax_category'])) {
            $taxCategory = $this->taxCategoryRepository->findOneBy(array('name' => $data['tax_category']));
            $product->setTaxCategory($taxCategory);
        }
        if (!empty($data['shipping_category'])) {
            $shippingCategory = $this->shippingCategoryRepository->find($data['shipping_category']);
            $product->setShippingCategory($shippingCategory);
        }
        if (isset($data['is_available_on'])) {
            $product->setAvailableOn(new \DateTime($data['is_available_on']));
        }
        if (isset($data['meta_keywords'])) {
            $product->setMetaKeyWords($data['meta_keywords']);
        }
        if (isset($data['meta_description'])) {
            $product->setMetaDescription($data['meta_description']);
        }
        if (isset($data['createdAt'])) {
            $product->setCreatedAt(new \DateTime($data['createdAt']));
        }

        return $product;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'product';
    }
}