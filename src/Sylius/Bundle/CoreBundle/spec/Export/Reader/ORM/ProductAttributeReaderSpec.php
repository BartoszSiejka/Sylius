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
use Sylius\Component\Attribute\Model\AttributeInterface;
use Iterator;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductAttributeReaderSpec extends ObjectBehavior
{
    function let(EntityRepository $productAttributeRepository, ArrayIteratorFactoryInterface $iteratorFactory)
    {
        $this->beConstructedWith($productAttributeRepository, $iteratorFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Export\Reader\ORM\ProductAttributeReader');
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
        $this->getType()->shouldReturn('product_attribute');
    }
    
    function it_exports_groups_to_csv_file(
        $productAttributeRepository,
        AbstractQuery $query, 
        QueryBuilder $queryBuilder,
        Logger $logger,
        AttributeInterface $attribute,
        AttributeInterface $attribute2,
        Iterator $arrayIterator,
        \DateTime $date,
        \DateTime $date2,
        $iteratorFactory
    ) {
        $productAttributeRepository
            ->createQueryBuilder('pa')
            ->willReturn($queryBuilder)
        ;
        
        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;
        
        $this->setConfiguration(array('batch_size' => 1), $logger);
        
        $attribute->getCreatedAt()->willReturn($date);
        $date->format('Y-m-d H:m:s')->willReturn('2014-02-03 14:20:13');
        $attribute->getId()->willReturn(1);
        $attribute->getName()->willReturn('attribute');
        $attribute->getType()->willReturn('text');
        $attribute->getPresentation()->willReturn('ati');
        
        $attribute2->getCreatedAt()->willReturn($date2);
        $date2->format('Y-m-d H:m:s')->willReturn('2014-03-03 14:20:13');
        $attribute2->getId()->willReturn(2);
        $attribute2->getName()->willReturn('attribute2');
        $attribute2->getType()->willReturn('text');
        $attribute2->getPresentation()->willReturn('ati2');
        
        $array = array($attribute, $attribute2);
        
        $returnArray = array(
            array(
                'id' => 1,
                'name' => 'attribute',
                'type' => 'text',
                'created_at' => '2014-02-03 14:20:13',
                'presentation' => 'ati',
            )
        );
        
        $returnArray2 = array(
            array(
                'id' => 2,
                'name' => 'attribute2',
                'type' => 'text',
                'created_at' => '2014-03-03 14:20:13',
                'presentation' => 'ati2',
            )
        );
        
        $query->execute()->willReturn($array);
        $iteratorFactory->createIteratorFromArray($array)->willReturn($arrayIterator);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($attribute);
        $arrayIterator->next()->shouldBeCalled();
        $attribute->getId()->shouldBeCalled();
        $attribute->getName()->shouldBeCalled();
        $attribute->getType()->shouldBeCalled();
        $attribute->getCreatedAt()->shouldBeCalled();
        $attribute->getPresentation()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($attribute2);
        $arrayIterator->next()->shouldBeCalled();
        $attribute2->getId()->shouldBeCalled();
        $attribute2->getName()->shouldBeCalled();
        $attribute2->getType()->shouldBeCalled();
        $attribute2->getCreatedAt()->shouldBeCalled();
        $attribute2->getPresentation()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray2);
        
        $arrayIterator->valid()->willReturn(false);
        $this->read()->shouldReturn(null);
    }
    
    function it_exports_groups_to_csv_file_with_batch_size_greater_than_1(
        $productAttributeRepository,
        AbstractQuery $query, 
        QueryBuilder $queryBuilder,
        Logger $logger,
        AttributeInterface $attribute,
        AttributeInterface $attribute2,
        Iterator $arrayIterator,
        \DateTime $date,
        \DateTime $date2,
        $iteratorFactory
    ) {
        $productAttributeRepository
            ->createQueryBuilder('pa')
            ->willReturn($queryBuilder)
        ;
        
        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;
        
        $this->setConfiguration(array('batch_size' => 2), $logger);
        
        $attribute->getCreatedAt()->willReturn($date);
        $date->format('Y-m-d H:m:s')->willReturn('2014-02-03 14:20:13');
        $attribute->getId()->willReturn(1);
        $attribute->getName()->willReturn('attribute');
        $attribute->getType()->willReturn('text');
        $attribute->getPresentation()->willReturn('ati');
        
        $attribute2->getCreatedAt()->willReturn($date2);
        $date2->format('Y-m-d H:m:s')->willReturn('2014-03-03 14:20:13');
        $attribute2->getId()->willReturn(2);
        $attribute2->getName()->willReturn('attribute2');
        $attribute2->getType()->willReturn('text');
        $attribute2->getPresentation()->willReturn('ati2');
        
        $array = array($attribute, $attribute2);
        
        $returnArray = array(
            array(
                'id' => 1,
                'name' => 'attribute',
                'type' => 'text',
                'created_at' => '2014-02-03 14:20:13',
                'presentation' => 'ati',
            ),
            array(
                'id' => 2,
                'name' => 'attribute2',
                'type' => 'text',
                'created_at' => '2014-03-03 14:20:13',
                'presentation' => 'ati2',
            )
        );
        
        $query->execute()->willReturn($array);
        $iteratorFactory->createIteratorFromArray($array)->willReturn($arrayIterator);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($attribute, $attribute2);
        $arrayIterator->next()->shouldBeCalled();
        $attribute->getId()->shouldBeCalled();
        $attribute->getName()->shouldBeCalled();
        $attribute->getType()->shouldBeCalled();
        $attribute->getCreatedAt()->shouldBeCalled();
        $attribute->getPresentation()->shouldBeCalled();
        
        $attribute2->getId()->shouldBeCalled();
        $attribute2->getName()->shouldBeCalled();
        $attribute2->getType()->shouldBeCalled();
        $attribute2->getCreatedAt()->shouldBeCalled();
        $attribute2->getPresentation()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray);
        
        $arrayIterator->valid()->willReturn(false);
        $this->read()->shouldReturn(null);
    }
}
