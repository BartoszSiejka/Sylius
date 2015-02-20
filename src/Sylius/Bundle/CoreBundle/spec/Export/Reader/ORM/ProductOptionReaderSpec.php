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
use Sylius\Component\Variation\Model\OptionInterface;
use Iterator;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductOptionReaderSpec extends ObjectBehavior
{
    function let(EntityRepository $productOptionRepository, ArrayIteratorFactoryInterface $iteratorFactory)
    {
        $this->beConstructedWith($productOptionRepository, $iteratorFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Export\Reader\ORM\ProductOptionReader');
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
        $this->getType()->shouldReturn('product_option');
    }
    
    function it_exports_groups_to_csv_file(
        $productOptionRepository,
        AbstractQuery $query, 
        QueryBuilder $queryBuilder,
        Logger $logger,
        OptionInterface $option,
        OptionInterface $option2,
        \DateTime $date,
        \DateTime $date2,
        Iterator $arrayIterator,
        $iteratorFactory
    ) {
        $productOptionRepository
            ->createQueryBuilder('po')
            ->willReturn($queryBuilder)
        ;
        
        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;
        
        $this->setConfiguration(array('batch_size' => 1), $logger);
        
        $option->getCreatedAt()->willReturn($date);
        $date->format('Y-m-d H:m:s')->willReturn('2014-02-03 12:02:03');
        $option->getId()->willReturn(1);
        $option->getName()->willReturn('option');
        $option->getPresentation()->willReturn('opt');
        
        $option2->getCreatedAt()->willReturn($date2);
        $date2->format('Y-m-d H:m:s')->willReturn('2014-03-03 12:02:03');
        $option2->getId()->willReturn(2);
        $option2->getName()->willReturn('option2');
        $option2->getPresentation()->willReturn('opt2');
        
        $array = array($option, $option2);
        
        $returnArray = array(
            array(
                'id' => 1,
                'name' => 'option',
                'created_at' => '2014-02-03 12:02:03',
                'presentation' => 'opt'
            )
        );
        
        $returnArray2 = array(
            array(
                'id' => 2,
                'name' => 'option2',
                'created_at' => '2014-03-03 12:02:03',
                'presentation' => 'opt2'
            )
        );
        
        $query->execute()->willReturn($array);
        $iteratorFactory->createIteratorFromArray($array)->willReturn($arrayIterator);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($option);
        $arrayIterator->next()->shouldBeCalled();
        $option->getId()->shouldBeCalled();
        $option->getName()->shouldBeCalled();
        $option->getCreatedAt()->shouldBeCalled();
        $option->getPresentation()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($option2);
        $arrayIterator->next()->shouldBeCalled();
        $option2->getId()->shouldBeCalled();
        $option2->getName()->shouldBeCalled();
        $option2->getCreatedAt()->shouldBeCalled();
        $option2->getPresentation()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray2);
        
        $arrayIterator->valid()->willReturn(false);
        $this->read()->shouldReturn(null);
    }
    
    function it_exports_groups_to_csv_file_with_batch_size_grater_than_1(
        $productOptionRepository,
        AbstractQuery $query, 
        QueryBuilder $queryBuilder,
        Logger $logger,
        OptionInterface $option,
        OptionInterface $option2,
        \DateTime $date,
        \DateTime $date2,
        Iterator $arrayIterator,
        $iteratorFactory
    ) {
        $productOptionRepository
            ->createQueryBuilder('po')
            ->willReturn($queryBuilder)
        ;
        
        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;
        
        $this->setConfiguration(array('batch_size' => 2), $logger);
        
        $option->getCreatedAt()->willReturn($date);
        $date->format('Y-m-d H:m:s')->willReturn('2014-02-03 12:02:03');
        $option->getId()->willReturn(1);
        $option->getName()->willReturn('option');
        $option->getPresentation()->willReturn('opt');
        
        $option2->getCreatedAt()->willReturn($date2);
        $date2->format('Y-m-d H:m:s')->willReturn('2014-03-03 12:02:03');
        $option2->getId()->willReturn(2);
        $option2->getName()->willReturn('option2');
        $option2->getPresentation()->willReturn('opt2');
        
        $array = array($option, $option2);
        
        $returnArray = array(
            array(
                'id' => 1,
                'name' => 'option',
                'created_at' => '2014-02-03 12:02:03',
                'presentation' => 'opt'
            ),
            array(
                'id' => 2,
                'name' => 'option2',
                'created_at' => '2014-03-03 12:02:03',
                'presentation' => 'opt2'
            )
        );
        
        $query->execute()->willReturn($array);
        $iteratorFactory->createIteratorFromArray($array)->willReturn($arrayIterator);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($option, $option2);
        $arrayIterator->next()->shouldBeCalled();
        $option->getId()->shouldBeCalled();
        $option->getName()->shouldBeCalled();
        $option->getCreatedAt()->shouldBeCalled();
        $option->getPresentation()->shouldBeCalled();
        
        $option2->getId()->shouldBeCalled();
        $option2->getName()->shouldBeCalled();
        $option2->getCreatedAt()->shouldBeCalled();
        $option2->getPresentation()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray);
        
        $arrayIterator->valid()->willReturn(false);
        $this->read()->shouldReturn(null);
    }
}
