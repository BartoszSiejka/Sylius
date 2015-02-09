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
 * User writer.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class UserWriter extends AbstractDoctrineWriter
{
    private $userRepository;
    
    public function __construct(RepositoryInterface $userRepository, EntityManager $em)
    {
        parent::__construct($em);
        $this->userRepository = $userRepository;
    }
    
    public function process($data) 
    {
        $user = $this->userRepository->createNew();
        $shippingAddress = $user->getShippingAddress();
        $billingAddress = $user->getBillingAddress();
        
        $user->setFirstName($data['1']);
        $user->setLastName($data['2']);
        $user->setUsername($data['3']);
        $user->setEmail($data['4']);
        $shippingAddress ? $shippingAddress->setCompany($data['5']) : null;
        $shippingAddress ? $shippingAddress->setCountry($data['6']) : null;
        $shippingAddress ? $shippingAddress->setProvince($data['7']) : null;
        $shippingAddress ? $shippingAddress->setCity($data['8']) : null;
        $shippingAddress ? $shippingAddress->setStreet($data['9']) : null;
        $shippingAddress ? $shippingAddress->setPostcode($data['10']) : null;
        $shippingAddress ? $shippingAddress->setPhoneNumber($data['11']) : null;
        $billingAddress ? $billingAddress->setCompany($data['12']) : null;
        $billingAddress ? $billingAddress->setCountry($data['13']) : null;
        $billingAddress ? $billingAddress->setProvince($data['14']) : null;
        $billingAddress ? $billingAddress->setCity($data['15']) : null;
        $billingAddress ? $billingAddress->setStreet($data['16']) : null;
        $billingAddress ? $billingAddress->setPostcode($data['17']) : null;
        $billingAddress ? $billingAddress->setPhoneNumber($data['18']) : null;
        $user->setEnabled($data['19']);
        $user->setCurrency($data['20']);
        $user->setCreatedAt(new \DateTime($data['21']));
        
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'import_user';
    }
}