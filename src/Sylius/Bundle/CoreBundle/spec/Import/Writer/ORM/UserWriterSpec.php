<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Import\Writer\ORM;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\User;
use Sylius\Component\Addressing\Model\Address;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\UserRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\TranslatableEntityRepository;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductOptionWriterSpec extends ObjectBehavior
{
    function let(
        UserRepository $userRepository, 
        EntityRepository $addressRepository, 
        TranslatableEntityRepository $countryRepository, 
        EntityRepository $provinceRepository,
        EntityManager $em, 
        Logger $logger
    ) {
        $configuration = array('update' => 1);
        
        $this->beConstructedWith($userRepository, $addressRepository, $countryRepository, $provinceRepository, $em);
        $this->setConfiguration($configuration, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\UserWriter');
    }

    function it_is_abstract_doctrine_writer_object()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\AbstractDoctrineWriter');
    }

    function it_implements_writer_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Writer\WriterInterface');
    }

    function it_creates_new_user_if_it_does_not_exist(
        $userRepository, 
        $addressRepository, 
        $countryRepository, 
        $provinceRepository, 
        User $user, 
        Address $address,
        Address $country,
        Address $province
    ) {
        $data = array(
            'id'                            => 1,
            'first_name'                    => 'Jack',
            'last_name'                     => 'Strong',
            'username'                      => 'jack.strong@example.com',
            'email'                         => 'jack.strong@example.com',
            'shipping_address_company'      => 'company',
            'shipping_address_country'      => 'EN',
            'shipping_address_province'     => '',
            'shipping_address_city'         => 'Lodz',
            'shipping_address_street'       => 'Piekna',
            'shipping_address_postcode'     => '99999',
            'shipping_address_phone_number' => '585222512',
            'billing_address_company'       => 'company1',
            'billing_address_country'       => 'US',
            'billing_address_province'      => '',
            'billing_address_city'          => 'Los Angeles',
            'billing_address_street'        => 'Long',
            'billing_address_postcode'      => '555',
            'billing_address_phone_number'  => '7777777',
            'enabled'                       => 1,
            'currency'                      => 'EUR',
            'password'                      => 'password',
            'created_at'                    => '2015-02-10 10:02:09'
        );

        $userRepository->findOneBy(array('email' => 'jack.strong@example.com'))->willReturn(null);
        $userRepository->createNew()->willReturn($user);

        $shippingAddress = $addressRepository->createNew();
        $billingAddress = $addressRepository->createNew();
        
        $shippingCountry = $countryRepository->findOneByIsoName('EN');
        $shippingProvince = $provinceRepository->findOneByIsoName(null);
        $billingCountry = $countryRepository->findOneByIsoName('US');
        $billingProvince = $provinceRepository->findOneByIsoName('');
        
        $user->setFirstName('Jack');
        $user->setLastName('Strong');
        $user->setEmail('jack.strong@example.com');
        $shippingAddress->setCompany('company');
        $shippingAddress->setFirstName('Jack');
        $shippingAddress->setLastName('Strong');
        $shippingAddress->setCountry($shippingCountry);
        $shippingAddress->setProvince($shippingProvince);
        $shippingAddress->setCity('Lodz');
        $shippingAddress->setStreet('Piekna');
        $shippingAddress->setPostcode('99999');
        $shippingAddress->setPhoneNumber('585222512');
        $user->setShippingAddress($shippingAddress);
        $billingAddress->setCompany('company1');
        $billingAddress->setFirstName('Jack');
        $billingAddress->setLastName('Strong');
        $billingAddress->setCountry($billingCountry);
        $billingAddress->setProvince($billingProvince);
        $billingAddress->setCity('Los Angeles');
        $billingAddress->setStreet('Long');
        $billingAddress->setPostcode('555');
        $billingAddress->setPhoneNumber('7777777');
        $user->setBillingAddress($billingAddress);
        $user->setEnabled(1);
        $user->setCurrency('EUR');
        $user->setPlainPassword('password');
        $user->setCreatedAt('2015-02-10 10:02:09');
        
        $this->process($data)->shouldReturn($user);
    }

    function it_updates_user_if_it_exists(
        $userRepository, 
        $addressRepository, 
        $countryRepository, 
        $provinceRepository, 
        User $user, 
        Address $address,
        Address $country,
        Address $province
    ) {
        $data = array(
            'id'                            => 1,
            'first_name'                    => 'Jack',
            'last_name'                     => 'Strong',
            'username'                      => 'jack.strong@example.com',
            'email'                         => 'jack.strong@example.com',
            'shipping_address_company'      => 'company',
            'shipping_address_country'      => 'EN',
            'shipping_address_province'     => '',
            'shipping_address_city'         => 'Lodz',
            'shipping_address_street'       => 'Piekna',
            'shipping_address_postcode'     => '99999',
            'shipping_address_phone_number' => '585222512',
            'billing_address_company'       => 'company1',
            'billing_address_country'       => 'US',
            'billing_address_province'      => '',
            'billing_address_city'          => 'Los Angeles',
            'billing_address_street'        => 'Long',
            'billing_address_postcode'      => '555',
            'billing_address_phone_number'  => '7777777',
            'enabled'                       => 1,
            'currency'                      => 'EUR',
            'password'                      => 'password',
            'created_at'                    => '2015-02-10 10:02:09'
        );

        $userRepository->findOneBy(array('email' => 'jack.strong#example.com'))->willReturn($user);
        $userRepository->createNew()->shouldNotBeCalled();

        $shippingAddress = $addressRepository->createNew();
        $billingAddress = $addressRepository->createNew();
        
        $shippingCountry = $countryRepository->findOneByIsoName('EN');
        $shippingProvince = $provinceRepository->findOneByIsoName(null);
        $billingCountry = $countryRepository->findOneByIsoName('US');
        $billingProvince = $provinceRepository->findOneByIsoName('');
        
        $user->setFirstName('Jack');
        $user->setLastName('Strong');
        $user->setEmail('jack.strong@example.com');
        $shippingAddress->setCompany('company');
        $shippingAddress->setFirstName('Jack');
        $shippingAddress->setLastName('Strong');
        $shippingAddress->setCountry($shippingCountry);
        $shippingAddress->setProvince($shippingProvince);
        $shippingAddress->setCity('Lodz');
        $shippingAddress->setStreet('Piekna');
        $shippingAddress->setPostcode('99999');
        $shippingAddress->setPhoneNumber('585222512');
        $user->setShippingAddress($shippingAddress);
        $billingAddress->setCompany('company1');
        $billingAddress->setFirstName('Jack');
        $billingAddress->setLastName('Strong');
        $billingAddress->setCountry($billingCountry);
        $billingAddress->setProvince($billingProvince);
        $billingAddress->setCity('Los Angeles');
        $billingAddress->setStreet('Long');
        $billingAddress->setPostcode('555');
        $billingAddress->setPhoneNumber('7777777');
        $user->setBillingAddress($billingAddress);
        $user->setEnabled(1);
        $user->setCurrency('EUR');
        $user->setPlainPassword('password');
        $user->setCreatedAt('2015-02-10 10:02:09');

        $this->process($data)->shouldReturn($user);
    }
    
    function it_has_type()
    {
        $this->getType()->shouldReturn('user');
    }
}
