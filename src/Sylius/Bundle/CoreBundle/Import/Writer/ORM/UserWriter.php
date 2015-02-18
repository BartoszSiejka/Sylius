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
    private $addressRepository;
    private $countryRepository;
    private $provinceRepository;
    
    public function __construct(
        RepositoryInterface $userRepository, 
        RepositoryInterface $addressRepository, 
        RepositoryInterface $countryRepository, 
        RepositoryInterface $provinceRepository,
        EntityManager $em
    ) {
        parent::__construct($em);
        $this->userRepository = $userRepository;
        $this->addressRepository = $addressRepository;
        $this->countryRepository = $countryRepository;
        $this->provinceRepository = $provinceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'user';
    }

    protected function process($data)
    {
        $userRepository = $this->userRepository;

        if ($user = $userRepository->findOneBy(array('email' => $data['email']))) {
            $user->getShippingAddress() ? $shippingAddress = $user->getShippingAddress() : $shippingAddress = $this->addressRepository->createNew();
            $user->getBillingAddress() ? $billingAddress = $user->getBillingAddress() : $billingAddress = $this->addressRepository->createNew();

            $data['shipping_address_country'] ? $shippingCountry = $this->countryRepository->findOneBy(array('isoName' => $data['shipping_address_country'])) : $shippingCountry = null;
            $data['shipping_address_province'] ? $shippingProvince = $this->provinceRepository->findOneBy(array('isoName' => $data['shipping_address_province'])) : $shippingProvince = null;
            $data['billing_address_country'] ? $billingCountry = $this->countryRepository->findOneBy(array('isoName' => $data['billing_address_country'])) : $billingCountry = null;
            $data['billing_address_province'] ? $billingProvince = $this->provinceRepository->findOneBy(array('isoName' => $data['billing_address_province'])) : $billingProvince = null;

            $data['first_name'] ? $user->setFirstName($data['first_name']) : null;
            $data['last_name'] ? $user->setLastName($data['last_name']) : null;
            $data['email'] ? $user->setEmail($data['email']) : null;
            $data['shipping_address_company'] ? $shippingAddress->setCompany($data['shipping_address_company']) : null;
            $data['first_name'] ? $shippingAddress->setFirstName($data['first_name']) : null;
            $data['last_name'] ? $shippingAddress->setLastName($data['last_name']) : null;
            $shippingCountry ? $shippingAddress->setCountry($shippingCountry) : null;
            $shippingProvince ? $shippingAddress->setProvince($shippingProvince) : null;
            $data['shipping_address_city'] ? $shippingAddress->setCity($data['shipping_address_city']) : null;
            $data['shipping_address_street'] ? $shippingAddress->setStreet($data['shipping_address_street']) : null;
            $data['shipping_address_postcode'] ? $shippingAddress->setPostcode($data['shipping_address_postcode']) : null;
            $data['shipping_address_phone_number'] ? $shippingAddress->setPhoneNumber($data['shipping_address_phone_number']) : null;
            $user->setShippingAddress($shippingAddress);
            $data['billing_address_company'] ? $billingAddress->setCompany($data['billing_address_company']) : null;
            $data['first_name'] ? $billingAddress->setFirstName($data['first_name']) : null;
            $data['last_name'] ? $billingAddress->setLastName($data['last_name']) : null;
            $billingCountry ? $billingAddress->setCountry($billingCountry) : null;
            $billingProvince ? $billingAddress->setProvince($billingProvince) : null;
            $data['billing_address_city'] ? $billingAddress->setCity($data['billing_address_city']) : null;
            $data['billing_address_street'] ? $billingAddress->setStreet($data['billing_address_street']) : null;
            $data['billing_address_postcode'] ? $billingAddress->setPostcode($data['billing_address_postcode']) : null;
            $data['billing_address_phone_number'] ? $billingAddress->setPhoneNumber($data['billing_address_phone_number']) : null;
            $user->setBillingAddress($billingAddress);
            $user->setEnabled($data['enabled']);
            $data['currency'] ? $user->setCurrency($data['currency']) : null;
            $user->setPlainPassword($data['password']);
            $user->setUpdatedAt(new \DateTime());

            return $user;
        }
        
        $user = $userRepository->createNew();
        $shippingAddress = $this->addressRepository->createNew();
        $billingAddress = $this->addressRepository->createNew();
        $data['shipping_address_country'] ? $shippingCountry = $this->countryRepository->findOneBy(array('isoName' => $data['shipping_address_country'])) : $shippingCountry = null;
        $data['shipping_address_province'] ? $shippingProvince = $this->provinceRepository->findOneBy(array('isoName' => $data['shipping_address_province'])) : $shippingProvince = null;
        $data['billing_address_country'] ? $billingCountry = $this->countryRepository->findOneBy(array('isoName' => $data['billing_address_country'])) : $billingCountry = null;
        $data['billing_address_province'] ? $billingProvince = $this->provinceRepository->findOneBy(array('isoName' => $data['billing_address_province'])) : $billingProvince = null;
        
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($data['email']);
        $data['shipping_address_company'] ? $shippingAddress->setCompany($data['shipping_address_company']) : null;
        $shippingAddress->setFirstName($data['first_name']);
        $shippingAddress->setLastName($data['last_name']);
        $shippingAddress->setCountry($shippingCountry);
        $shippingAddress->setProvince($shippingProvince);
        $data['shipping_address_city'] ? $shippingAddress->setCity($data['shipping_address_city']) : null;
        $data['shipping_address_street'] ? $shippingAddress->setStreet($data['shipping_address_street']) : null;
        $data['shipping_address_postcode'] ? $shippingAddress->setPostcode($data['shipping_address_postcode']) : null;
        $data['shipping_address_phone_number'] ? $shippingAddress->setPhoneNumber($data['shipping_address_phone_number']) : null;
        $user->setShippingAddress($shippingAddress);
        $data['billing_address_company'] ? $billingAddress->setCompany($data['billing_address_company']) : null;
        $billingAddress->setFirstName($data['first_name']);
        $billingAddress->setLastName($data['last_name']);
        $billingAddress->setCountry($billingCountry);
        $billingAddress->setProvince($billingProvince);
        $data['billing_address_city'] ? $billingAddress->setCity($data['billing_address_city']) : null;
        $data['billing_address_street'] ? $billingAddress->setStreet($data['billing_address_street']) : null;
        $data['billing_address_postcode'] ? $billingAddress->setPostcode($data['billing_address_postcode']) : null;
        $data['billing_address_phone_number'] ? $billingAddress->setPhoneNumber($data['billing_address_phone_number']) : null;
        $user->setBillingAddress($billingAddress);
        $user->setEnabled($data['enabled']);
        $user->setCurrency($data['currency']);
        $user->setPlainPassword($data['password']);
        $user->setCreatedAt(new \DateTime($data['created_at']));
        
        return $user;
    }
}
