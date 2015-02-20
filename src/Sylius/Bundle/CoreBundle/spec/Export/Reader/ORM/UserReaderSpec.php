<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Export\Reader\ORM;

use PhpSpec\ObjectBehavior;
use Doctrine\ORM\EntityRepository;
use Sylius\Component\ImportExport\Factory\ArrayIteratorFactoryInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Monolog\Logger;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Iterator;
/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductOptionReaderSpec extends ObjectBehavior
{
    function let(EntityRepository $userRepository, ArrayIteratorFactoryInterface $iteratorFactory)
    {
        $this->beConstructedWith($userRepository, $iteratorFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Export\Reader\ORM\UserReader');
    }

    function it_extends_abstract_doctrine_reader_object()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Export\Reader\ORM\AbstractDoctrineReader');
    }

    function it_implements_reader_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Reader\ReaderInterface');
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('user');
    }
    
    function it_exports_groups_to_csv_file(
        $userRepository,
        AbstractQuery $query, 
        QueryBuilder $queryBuilder,
        Logger $logger,
        UserInterface $user,
        UserInterface $user2,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress2,
        AddressInterface $billingAddress2,
        \DateTime $date,
        \DateTime $date2,
        Iterator $arrayIterator,
        $iteratorFactory
    ) {
        $userRepository
            ->createQueryBuilder('u')
            ->willReturn($queryBuilder)
        ;
        
        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;
        
        $this->setConfiguration(array('batch_size' => 1), $logger);
        
        $user->getShippingAddress()->willReturn($shippingAddress);
        $user->getBillingAddress()->willReturn($billingAddress);
        $user->getCreatedAt()->willReturn($date);
        $date->format('Y-m-d H:m:s')->willReturn('2014-02-03 12:02:03');
        $user->getId()->willReturn(1);
        $user->getFirstName()->willReturn('John');
        $user->getLastName()->willReturn('Malkovic');
        $user->getUsername()->willReturn('jm');
        $user->getEmail()->willReturn('jm@example.com');
        $shippingAddress->getCompany()->willReturn('');
        $shippingAddress->getCountry()->willReturn('PL');
        $shippingAddress->getProvince()->willReturn('');
        $shippingAddress->getCity()->willReturn('Warsaw');
        $shippingAddress->getStreet()->willReturn('New world');
        $shippingAddress->getPostcode()->willReturn('00-000');
        $shippingAddress->getPhoneNumber()->willReturn('');
        $billingAddress->getCompany()->willReturn('company');
        $billingAddress->getCountry()->willReturn('EN');
        $billingAddress->getProvince()->willReturn('');
        $billingAddress->getCity()->willReturn('London');
        $billingAddress->getStreet()->willReturn('Bakery Street');
        $billingAddress->getPostcode()->willReturn('8888');
        $billingAddress->getPhoneNumber()->willReturn('545421');
        $user->isEnabled()->willReturn(1);
        $user->getCurrency()->willReturn('EUR');
        
        $user2->getShippingAddress()->willReturn($shippingAddress2);
        $user2->getBillingAddress()->willReturn($billingAddress2);
        $user2->getCreatedAt()->willReturn($date2);
        $date2->format('Y-m-d H:m:s')->willReturn('2014-03-03 12:02:03');
        $user2->getId()->willReturn(2);
        $user2->getFirstName()->willReturn('John2');
        $user2->getLastName()->willReturn('Malkovic2');
        $user2->getUsername()->willReturn('jm2');
        $user2->getEmail()->willReturn('jm@example.com2');
        $shippingAddress2->getCompany()->willReturn('');
        $shippingAddress2->getCountry()->willReturn('PL');
        $shippingAddress2->getProvince()->willReturn('');
        $shippingAddress2->getCity()->willReturn('Warsaw2');
        $shippingAddress2->getStreet()->willReturn('New world2');
        $shippingAddress2->getPostcode()->willReturn('00-0002');
        $shippingAddress2->getPhoneNumber()->willReturn('');
        $billingAddress2->getCompany()->willReturn('company2');
        $billingAddress2->getCountry()->willReturn('EN');
        $billingAddress2->getProvince()->willReturn('');
        $billingAddress2->getCity()->willReturn('London2');
        $billingAddress2->getStreet()->willReturn('Bakery Street2');
        $billingAddress2->getPostcode()->willReturn('88882');
        $billingAddress2->getPhoneNumber()->willReturn('5454212');
        $user2->isEnabled()->willReturn(0);
        $user2->getCurrency()->willReturn('EUR');
        
        $array = array($user, $user2);
        
        $returnArray = array(
            array(
                'id'                            => 1,
                'first_name'                    => 'John',
                'last_name'                     => 'Malkovic',
                'username'                      => 'jm',
                'email'                         => 'jm@example.com',
                'shipping_address_company'      => '',
                'shipping_address_country'      => 'PL',
                'shipping_address_province'     => '',
                'shipping_address_city'         => 'Warsaw',
                'shipping_address_street'       => 'New world',
                'shipping_address_postcode'     => '00-000',
                'shipping_address_phone_number' => '',
                'billing_address_company'       => 'company',
                'billing_address_country'       => 'EN',
                'billing_address_province'      => '',
                'billing_address_city'          => 'London',
                'billing_address_street'        => 'Bakery Street',
                'billing_address_postcode'      => '8888',
                'billing_address_phone_number'  => '545421',
                'enabled'                       => 1,
                'currency'                      => 'EUR',
                'created_at'                    => '2014-02-03 12:02:03'
            )
        );
        
        $returnArray2 = array(
            array(
                'id'                            => 2,
                'first_name'                    => 'John2',
                'last_name'                     => 'Malkovic2',
                'username'                      => 'jm2',
                'email'                         => 'jm@example.com2',
                'shipping_address_company'      => '',
                'shipping_address_country'      => 'PL',
                'shipping_address_province'     => '',
                'shipping_address_city'         => 'Warsaw2',
                'shipping_address_street'       => 'New world2',
                'shipping_address_postcode'     => '00-0002',
                'shipping_address_phone_number' => '',
                'billing_address_company'       => 'company2',
                'billing_address_country'       => 'EN',
                'billing_address_province'      => '',
                'billing_address_city'          => 'London2',
                'billing_address_street'        => 'Bakery Street2',
                'billing_address_postcode'      => '88882',
                'billing_address_phone_number'  => '5454212',
                'enabled'                       => 1,
                'currency'                      => 'EUR',
                'created_at'                    => '2014-03-03 12:02:03'
            )
        );
        
        $query->execute()->willReturn($array);
        $iteratorFactory->createIteratorFromArray($array)->willReturn($arrayIterator);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($user);
        $arrayIterator->next()->shouldBeCalled();
        $user->getShippingAddress()->shouldBeCalled();
        $user->getBillingAddress()->shouldBeCalled();
        $user->getCreatedAt()->shouldBeCalled();
        $date->format('Y-m-d H:m:s')->shouldBeCalled();
        $user->getId()->shouldBeCalled();
        $user->getFirstName()->shouldBeCalled();
        $user->getLastName()->shouldBeCalled();
        $user->getUsername()->shouldBeCalled();
        $user->getEmail()->shouldBeCalled();
        $shippingAddress->getCompany()->shouldBeCalled();
        $shippingAddress->getCountry()->shouldBeCalled();
        $shippingAddress->getProvince()->shouldBeCalled();
        $shippingAddress->getCity()->shouldBeCalled();
        $shippingAddress->getStreet()->shouldBeCalled();
        $shippingAddress->getPostcode()->shouldBeCalled();
        $shippingAddress->getPhoneNumber()->shouldBeCalled();
        $billingAddress->getCompany()->shouldBeCalled();
        $billingAddress->getCountry()->shouldBeCalled();
        $billingAddress->getProvince()->shouldBeCalled();
        $billingAddress->getCity()->shouldBeCalled();
        $billingAddress->getStreet()->shouldBeCalled();
        $billingAddress->getPostcode()->shouldBeCalled();
        $billingAddress->getPhoneNumber()->shouldBeCalled();
        $user->isEnabled()->shouldBeCalled();
        $user->getCurrency()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($user2);
        $arrayIterator->next()->shouldBeCalled();
        $user2->getShippingAddress()->shouldBeCalled();
        $user2->getBillingAddress()->shouldBeCalled();
        $user2->getCreatedAt()->shouldBeCalled();
        $date2->format('Y-m-d H:m:s')->shouldBeCalled();
        $user2->getId()->shouldBeCalled();
        $user2->getFirstName()->shouldBeCalled();
        $user2->getLastName()->shouldBeCalled();
        $user2->getUsername()->shouldBeCalled();
        $user2->getEmail()->shouldBeCalled();
        $shippingAddress2->getCompany()->shouldBeCalled();
        $shippingAddress2->getCountry()->shouldBeCalled();
        $shippingAddress2->getProvince()->shouldBeCalled();
        $shippingAddress2->getCity()->shouldBeCalled();
        $shippingAddress2->getStreet()->shouldBeCalled();
        $shippingAddress2->getPostcode()->shouldBeCalled();
        $shippingAddress2->getPhoneNumber()->shouldBeCalled();
        $billingAddress2->getCompany()->shouldBeCalled();
        $billingAddress2->getCountry()->shouldBeCalled();
        $billingAddress2->getProvince()->shouldBeCalled();
        $billingAddress2->getCity()->shouldBeCalled();
        $billingAddress2->getStreet()->shouldBeCalled();
        $billingAddress2->getPostcode()->shouldBeCalled();
        $billingAddress2->getPhoneNumber()->shouldBeCalled();
        $user2->isEnabled()->shouldBeCalled();
        $user2->getCurrency()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray2);
        
        $arrayIterator->valid()->willReturn(false);
        $this->read()->shouldReturn(null);
    }
    
    function it_exports_groups_to_csv_file_with_batch_size_greater_than_1(
        $userRepository,
        AbstractQuery $query, 
        QueryBuilder $queryBuilder,
        Logger $logger,
        UserInterface $user,
        UserInterface $user2,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress2,
        AddressInterface $billingAddress2,
        \DateTime $date,
        \DateTime $date2,
        Iterator $arrayIterator,
        $iteratorFactory
    ) {
        $userRepository
            ->createQueryBuilder('u')
            ->willReturn($queryBuilder)
        ;
        
        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;
        
        $this->setConfiguration(array('batch_size' => 1), $logger);
        
        $user->getShippingAddress()->willReturn($shippingAddress);
        $user->getBillingAddress()->willReturn($billingAddress);
        $user->getCreatedAt()->willReturn($date);
        $date->format('Y-m-d H:m:s')->willReturn('2014-02-03 12:02:03');
        $user->getId()->willReturn(1);
        $user->getFirstName()->willReturn('John');
        $user->getLastName()->willReturn('Malkovic');
        $user->getUsername()->willReturn('jm');
        $user->getEmail()->willReturn('jm@example.com');
        $shippingAddress->getCompany()->willReturn('');
        $shippingAddress->getCountry()->willReturn('PL');
        $shippingAddress->getProvince()->willReturn('');
        $shippingAddress->getCity()->willReturn('Warsaw');
        $shippingAddress->getStreet()->willReturn('New world');
        $shippingAddress->getPostcode()->willReturn('00-000');
        $shippingAddress->getPhoneNumber()->willReturn('');
        $billingAddress->getCompany()->willReturn('company');
        $billingAddress->getCountry()->willReturn('EN');
        $billingAddress->getProvince()->willReturn('');
        $billingAddress->getCity()->willReturn('London');
        $billingAddress->getStreet()->willReturn('Bakery Street');
        $billingAddress->getPostcode()->willReturn('8888');
        $billingAddress->getPhoneNumber()->willReturn('545421');
        $user->isEnabled()->willReturn(1);
        $user->getCurrency()->willReturn('EUR');
        
        $user2->getShippingAddress()->willReturn($shippingAddress2);
        $user2->getBillingAddress()->willReturn($billingAddress2);
        $user2->getCreatedAt()->willReturn($date2);
        $date2->format('Y-m-d H:m:s')->willReturn('2014-03-03 12:02:03');
        $user2->getId()->willReturn(2);
        $user2->getFirstName()->willReturn('John2');
        $user2->getLastName()->willReturn('Malkovic2');
        $user2->getUsername()->willReturn('jm2');
        $user2->getEmail()->willReturn('jm@example.com2');
        $shippingAddress2->getCompany()->willReturn('');
        $shippingAddress2->getCountry()->willReturn('PL');
        $shippingAddress2->getProvince()->willReturn('');
        $shippingAddress2->getCity()->willReturn('Warsaw2');
        $shippingAddress2->getStreet()->willReturn('New world2');
        $shippingAddress2->getPostcode()->willReturn('00-0002');
        $shippingAddress2->getPhoneNumber()->willReturn('');
        $billingAddress2->getCompany()->willReturn('company2');
        $billingAddress2->getCountry()->willReturn('EN');
        $billingAddress2->getProvince()->willReturn('');
        $billingAddress2->getCity()->willReturn('London2');
        $billingAddress2->getStreet()->willReturn('Bakery Street2');
        $billingAddress2->getPostcode()->willReturn('88882');
        $billingAddress2->getPhoneNumber()->willReturn('5454212');
        $user2->isEnabled()->willReturn(0);
        $user2->getCurrency()->willReturn('EUR');
        
        $array = array($user, $user2);
        
        $returnArray = array(
            array(
                'id'                            => 1,
                'first_name'                    => 'John',
                'last_name'                     => 'Malkovic',
                'username'                      => 'jm',
                'email'                         => 'jm@example.com',
                'shipping_address_company'      => '',
                'shipping_address_country'      => 'PL',
                'shipping_address_province'     => '',
                'shipping_address_city'         => 'Warsaw',
                'shipping_address_street'       => 'New world',
                'shipping_address_postcode'     => '00-000',
                'shipping_address_phone_number' => '',
                'billing_address_company'       => 'company',
                'billing_address_country'       => 'EN',
                'billing_address_province'      => '',
                'billing_address_city'          => 'London',
                'billing_address_street'        => 'Bakery Street',
                'billing_address_postcode'      => '8888',
                'billing_address_phone_number'  => '545421',
                'enabled'                       => 1,
                'currency'                      => 'EUR',
                'created_at'                    => '2014-02-03 12:02:03'
            ),
            array(
                'id'                            => 2,
                'first_name'                    => 'John2',
                'last_name'                     => 'Malkovic2',
                'username'                      => 'jm2',
                'email'                         => 'jm@example.com2',
                'shipping_address_company'      => '',
                'shipping_address_country'      => 'PL',
                'shipping_address_province'     => '',
                'shipping_address_city'         => 'Warsaw2',
                'shipping_address_street'       => 'New world2',
                'shipping_address_postcode'     => '00-0002',
                'shipping_address_phone_number' => '',
                'billing_address_company'       => 'company2',
                'billing_address_country'       => 'EN',
                'billing_address_province'      => '',
                'billing_address_city'          => 'London2',
                'billing_address_street'        => 'Bakery Street2',
                'billing_address_postcode'      => '88882',
                'billing_address_phone_number'  => '5454212',
                'enabled'                       => 1,
                'currency'                      => 'EUR',
                'created_at'                    => '2014-03-03 12:02:03'
            )
        );
        
        $query->execute()->willReturn($array);
        $iteratorFactory->createIteratorFromArray($array)->willReturn($arrayIterator);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($user, $user2);
        $arrayIterator->next()->shouldBeCalled();
        $user->getShippingAddress()->shouldBeCalled();
        $user->getBillingAddress()->shouldBeCalled();
        $user->getCreatedAt()->shouldBeCalled();
        $date->format('Y-m-d H:m:s')->shouldBeCalled();
        $user->getId()->shouldBeCalled();
        $user->getFirstName()->shouldBeCalled();
        $user->getLastName()->shouldBeCalled();
        $user->getUsername()->shouldBeCalled();
        $user->getEmail()->shouldBeCalled();
        $shippingAddress->getCompany()->shouldBeCalled();
        $shippingAddress->getCountry()->shouldBeCalled();
        $shippingAddress->getProvince()->shouldBeCalled();
        $shippingAddress->getCity()->shouldBeCalled();
        $shippingAddress->getStreet()->shouldBeCalled();
        $shippingAddress->getPostcode()->shouldBeCalled();
        $shippingAddress->getPhoneNumber()->shouldBeCalled();
        $billingAddress->getCompany()->shouldBeCalled();
        $billingAddress->getCountry()->shouldBeCalled();
        $billingAddress->getProvince()->shouldBeCalled();
        $billingAddress->getCity()->shouldBeCalled();
        $billingAddress->getStreet()->shouldBeCalled();
        $billingAddress->getPostcode()->shouldBeCalled();
        $billingAddress->getPhoneNumber()->shouldBeCalled();
        $user->isEnabled()->shouldBeCalled();
        $user->getCurrency()->shouldBeCalled();
        
        $user2->getShippingAddress()->shouldBeCalled();
        $user2->getBillingAddress()->shouldBeCalled();
        $user2->getCreatedAt()->shouldBeCalled();
        $date2->format('Y-m-d H:m:s')->shouldBeCalled();
        $user2->getId()->shouldBeCalled();
        $user2->getFirstName()->shouldBeCalled();
        $user2->getLastName()->shouldBeCalled();
        $user2->getUsername()->shouldBeCalled();
        $user2->getEmail()->shouldBeCalled();
        $shippingAddress2->getCompany()->shouldBeCalled();
        $shippingAddress2->getCountry()->shouldBeCalled();
        $shippingAddress2->getProvince()->shouldBeCalled();
        $shippingAddress2->getCity()->shouldBeCalled();
        $shippingAddress2->getStreet()->shouldBeCalled();
        $shippingAddress2->getPostcode()->shouldBeCalled();
        $shippingAddress2->getPhoneNumber()->shouldBeCalled();
        $billingAddress2->getCompany()->shouldBeCalled();
        $billingAddress2->getCountry()->shouldBeCalled();
        $billingAddress2->getProvince()->shouldBeCalled();
        $billingAddress2->getCity()->shouldBeCalled();
        $billingAddress2->getStreet()->shouldBeCalled();
        $billingAddress2->getPostcode()->shouldBeCalled();
        $billingAddress2->getPhoneNumber()->shouldBeCalled();
        $user2->isEnabled()->shouldBeCalled();
        $user2->getCurrency()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray);
        
        $arrayIterator->valid()->willReturn(false);
        $this->read()->shouldReturn(null);
    }
}
