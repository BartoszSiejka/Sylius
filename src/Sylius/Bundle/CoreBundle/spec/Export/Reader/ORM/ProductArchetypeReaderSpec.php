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
use Sylius\Component\Product\Model\ArchetypeInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Monolog\Logger;
use Iterator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductArchetypeReaderSpec extends ObjectBehavior
{
    function let(EntityRepository $productArchetypeRepository, ArrayIteratorFactoryInterface $iteratorFactory)
    {
        $this->beConstructedWith($productArchetypeRepository, $iteratorFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Export\Reader\ORM\ProductArchetypeReader');
    }

    function it_is_abstract_doctrine_reader_object()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Export\Reader\ORM\AbstractDoctrineReader');
    }

    function it_implements_reader_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Reader\ReaderInterface');
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('product_archetype');
    }
    
    function it_exports_product_archetypes_to_csv_file(
        $productArchetypeRepository,
        AbstractQuery $query, 
        QueryBuilder $queryBuilder,
        Logger $logger,
        ArchetypeInterface $archetype,
        ArchetypeInterface $archetype2,
        ArchetypeInterface $parent,
        ArchetypeInterface $parent2,
        ArrayCollection $options,
        ArrayCollection $attributes,
        Iterator $arrayIterator,
        \DateTime $date,
        ArrayCollection $options2,
        ArrayCollection $attributes2,
        \DateTime $date2,
        $iteratorFactory
    ) {
        $productArchetypeRepository
            ->createQueryBuilder('pac')
            ->willReturn($queryBuilder)
        ;
        
        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;
        
        $this->setConfiguration(array('batch_size' => 1), $logger);
        
        $archetype->getOptions()->willReturn($options);
        $options->toArray()->willReturn(array('option'));
        $archetype->getAttributes()->willReturn($attributes);
        $attributes->toArray()->willReturn(array('attribute'));
        $archetype->getCreatedAt()->willReturn($date);
        $date->format('Y-m-d H:m:s')->willReturn('2014-02-02 12:01:03');
        $archetype->getId()->willReturn(1);
        $archetype->getCode()->willReturn('arch');
        $archetype->getName()->willReturn('archetype');
        $archetype->getParent()->willReturn($parent);
        $parent->getName()->willReturn('parent');
        
        $archetype2->getOptions()->willReturn($options2);
        $options2->toArray()->willReturn(array('option2'));
        $archetype2->getAttributes()->willReturn($attributes2);
        $attributes2->toArray()->willReturn(array('attribute2'));
        $archetype2->getCreatedAt()->willReturn($date2);
        $date2->format('Y-m-d H:m:s')->willReturn('2014-03-02 12:01:03');
        $archetype2->getId()->willReturn(2);
        $archetype2->getCode()->willReturn('arch2');
        $archetype2->getName()->willReturn('archetype2');
        $archetype2->getParent()->willReturn($parent2);
        $parent2->getName()->willReturn('parent2');
        
        $array = array($archetype, $archetype2);
        
        $returnArray = array(
            array(
                'id' => 1,
                'code' => 'arch',
                'name' => 'archetype',
                'parent'=> 'parent',
                'options' => 'option',
                'attributes' => 'attribute',
                'created_at' => '2014-02-02 12:01:03'
            )
        );
        
        $returnArray2 = array(
            array(
                'id' => 2,
                'code' => 'arch2',
                'name' => 'archetype2',
                'parent'=> 'parent2',
                'options' => 'option2',
                'attributes' => 'attribute2',
                'created_at' => '2014-03-02 12:01:03'
            )
        );
        
        $query->execute()->willReturn($array);
        $iteratorFactory->createIteratorFromArray($array)->willReturn($arrayIterator);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($archetype);
        $arrayIterator->next()->shouldBeCalled();
        $archetype->getOptions()->shouldBeCalled();
        $archetype->getAttributes()->shouldBeCalled();
        $archetype->getCreatedAt()->shouldBeCalled();
        $archetype->getId()->shouldBeCalled();
        $archetype->getCode()->shouldBeCalled();
        $archetype->getName()->shouldBeCalled();
        $parent->getName()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($archetype2);
        $arrayIterator->next()->shouldBeCalled();
        $archetype2->getOptions()->shouldBeCalled();
        $archetype2->getAttributes()->shouldBeCalled();
        $archetype2->getCreatedAt()->shouldBeCalled();
        $archetype2->getId()->shouldBeCalled();
        $archetype2->getCode()->shouldBeCalled();
        $archetype2->getName()->shouldBeCalled();
        $parent2->getName()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray2);
        
        $arrayIterator->valid()->willReturn(false);
        $this->read()->shouldReturn(null);
    }
    
    function it_exports_product_archetypes_to_csv_file_with_batch_size_greater_than_1(
        $productArchetypeRepository,
        AbstractQuery $query, 
        QueryBuilder $queryBuilder,
        Logger $logger,
        ArchetypeInterface $archetype,
        ArchetypeInterface $archetype2,
        ArchetypeInterface $parent,
        ArchetypeInterface $parent2,
        ArrayCollection $options,
        ArrayCollection $attributes,
        Iterator $arrayIterator,
        \DateTime $date,
        ArrayCollection $options2,
        ArrayCollection $attributes2,
        \DateTime $date2,
        $iteratorFactory
    ) {
        $productArchetypeRepository
            ->createQueryBuilder('pac')
            ->willReturn($queryBuilder)
        ;
        
        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;
        
        $this->setConfiguration(array('batch_size' => 2), $logger);
        
        $archetype->getOptions()->willReturn($options);
        $options->toArray()->willReturn(array('option'));
        $archetype->getAttributes()->willReturn($attributes);
        $attributes->toArray()->willReturn(array('attribute'));
        $archetype->getCreatedAt()->willReturn($date);
        $date->format('Y-m-d H:m:s')->willReturn('2014-02-02 12:01:03');
        $archetype->getId()->willReturn(1);
        $archetype->getCode()->willReturn('arch');
        $archetype->getName()->willReturn('archetype');
        $archetype->getParent()->willReturn($parent);
        $parent->getName()->willReturn('parent');
        
        $archetype2->getOptions()->willReturn($options2);
        $options2->toArray()->willReturn(array('option2'));
        $archetype2->getAttributes()->willReturn($attributes2);
        $attributes2->toArray()->willReturn(array('attribute2'));
        $archetype2->getCreatedAt()->willReturn($date2);
        $date2->format('Y-m-d H:m:s')->willReturn('2014-03-02 12:01:03');
        $archetype2->getId()->willReturn(2);
        $archetype2->getCode()->willReturn('arch2');
        $archetype2->getName()->willReturn('archetype2');
        $archetype2->getParent()->willReturn($parent2);
        $parent2->getName()->willReturn('parent2');
        
        $array = array($archetype, $archetype2);
        
        $returnArray = array(
            array(
                'id' => 1,
                'code' => 'arch',
                'name' => 'archetype',
                'parent'=> 'parent',
                'options' => 'option',
                'attributes' => 'attribute',
                'created_at' => '2014-02-02 12:01:03'
            ),
            array(
                'id' => 2,
                'code' => 'arch2',
                'name' => 'archetype2',
                'parent'=> 'parent2',
                'options' => 'option2',
                'attributes' => 'attribute2',
                'created_at' => '2014-03-02 12:01:03'
            )
        );
        
        $query->execute()->willReturn($array);
        $iteratorFactory->createIteratorFromArray($array)->willReturn($arrayIterator);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($archetype, $archetype2);
        $arrayIterator->next()->shouldBeCalled();
        $archetype->getOptions()->shouldBeCalled();
        $archetype->getAttributes()->shouldBeCalled();
        $archetype->getCreatedAt()->shouldBeCalled();
        $archetype->getId()->shouldBeCalled();
        $archetype->getCode()->shouldBeCalled();
        $archetype->getName()->shouldBeCalled();
        $parent->getName()->shouldBeCalled();
        
        $archetype2->getOptions()->shouldBeCalled();
        $archetype2->getAttributes()->shouldBeCalled();
        $archetype2->getCreatedAt()->shouldBeCalled();
        $archetype2->getId()->shouldBeCalled();
        $archetype2->getCode()->shouldBeCalled();
        $archetype2->getName()->shouldBeCalled();
        $parent2->getName()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray);
        
        $arrayIterator->valid()->willReturn(false);
        $this->read()->shouldReturn(null);
    }
}
