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
use Sylius\Component\Product\Model\Attribute;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\TranslatableEntityRepository;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductAttributeWriterSpec extends ObjectBehavior
{
    function let(TranslatableEntityRepository $productAttributeRepository, EntityManager $em, Logger $logger)
    {
        $configuration = array('update' => 1);
        
        $this->beConstructedWith($productAttributeRepository, $em);
        $this->setConfiguration($configuration, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\ProductAttributeWriter');
    }

    function it_is_abstract_doctrine_writer_object()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\AbstractDoctrineWriter');
    }

    function it_implements_writer_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Writer\WriterInterface');
    }

    function it_creates_new_attribute_if_it_does_not_exist($productAttributeRepository, Attribute $productAttribute)
    {
        $data = array(
            'id'            => 1,
            'name'          => 'testAttribute',
            'type'          => 'text',
            'created_at'    => '2015-02-10 10:02:09',
            'presentation'  => 'testPresentation',
        );

        $productAttributeRepository->findOneBy(array('name' => 'testAttribute'))->willReturn(null);
        $productAttributeRepository->createNew()->willReturn($productAttribute);

        $this->process($data);

        $productAttribute->setName('testAttribute')->shouldBeCalled();
        $productAttribute->setType('text')->shouldBeCalled();
        $productAttribute->setCreatedAt(new \DateTime('2015-02-10 10:02:09'))->shouldBeCalled();
        $productAttribute->setPresentation('testPresentation')->shouldBeCalled();
    }

    function it_updates_attribute_if_it_exists($productAttributeRepository, Attribute $productAttribute)
    {
        $data = array(
            'id'            => 1,
            'name'          => 'testAttribute',
            'type'          => null,
            'created_at'    => '2015-02-10 10:02:09',
            'presentation'  => 'testPresentation',
        );

        $productAttributeRepository->findOneBy(array('name' => 'testAttribute'))->willReturn($productAttribute);
        $productAttributeRepository->createNew()->shouldNotBeCalled();
        
        $this->process($data);

        $productAttribute->setName('testAttribute')->shouldBeCalled();
        $productAttribute->setType('text')->shouldNotBeCalled();
        $productAttribute->setCreatedAt(new \DateTime('2015-02-10 10:02:09'))->shouldBeCalled();
        $productAttribute->setPresentation('testPresentation')->shouldBeCalled();
    }
    
    function it_has_type()
    {
        $this->getType()->shouldReturn('product_attribute');
    }
}
