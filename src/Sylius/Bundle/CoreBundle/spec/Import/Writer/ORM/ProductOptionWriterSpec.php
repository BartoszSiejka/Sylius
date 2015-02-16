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
class ProductOptionWriterSpec extends ObjectBehavior
{
    function let(TranslatableEntityRepository $productOptionRepository, EntityManager $em, Logger $logger)
    {
        $configuration = array('update' => 1);
        
        $this->beConstructedWith($productOptionRepository, $em);
        $this->setConfiguration($configuration, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\ProductOptionWriter');
    }

    function it_is_abstract_doctrine_writer_object()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\AbstractDoctrineWriter');
    }

    function it_implements_writer_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Writer\WriterInterface');
    }

    function it_creates_new_attribute_if_it_does_not_exist($productOptionRepository, Attribute $productOption)
    {
        $data = array(
            'id'           => 1,
            'name'         => 'testOption',
            'created_at'   => '2015-02-10 10:02:09',
            'presentation' => '',
        );

        $productAttributeRepository->findOneBy(array('name' => 'testAttribute'))->willReturn(null);
        $productAttributeRepository->createNew()->willReturn($productAttribute);

        $productAttribute->setName('testAttribute');
        $productAttribute->setType('text');
        $productAttribute->setCreatedAt('2015-02-10 10:02:09');
        $productAttribute->setPresentation('testPresentation');

        $this->process($data)->shouldReturn($productAttribute);
    }

    function it_updates_attribute_if_it_exists($productAttributeRepository, Attribute $productAttribute)
    {
        $data = array(
            'id'            => 1,
            'name'          => 'testAttribute',
            'type'          => 'text',
            'created_at'    => '2015-02-10 10:02:09',
            'presentation'  => 'testPresentation',
        );

        $productAttributeRepository->findOneBy(array('name' => 'testAttribute'))->willReturn($productAttribute);
        $productAttributeRepository->createNew()->shouldNotBeCalled();
        
        $productAttribute->setName('testAttribute');
        $productAttribute->setType('text');
        $productAttribute->setCreatedAt('2015-02-10 10:02:09');
        $productAttribute->setPresentation('testPresentation');

        $this->process($data)->shouldReturn($productAttribute);
    }
    
    function it_has_type()
    {
        $this->getType()->shouldReturn('product_attribute');
    }
}
