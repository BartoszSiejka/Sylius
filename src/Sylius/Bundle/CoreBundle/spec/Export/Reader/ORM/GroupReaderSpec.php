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
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\AbstractQuery;
use Monolog\Logger;
use Sylius\Component\Core\Model\GroupInterface;
use Sylius\Component\ImportExport\Factory\ArrayIteratorFactoryInterface;
use Iterator;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class GroupReaderSpec extends ObjectBehavior
{
    function let(EntityRepository $groupRepository, ArrayIteratorFactoryInterface $iteratorFactory)
    {
        $this->beConstructedWith($groupRepository, $iteratorFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Export\Reader\ORM\GroupReader');
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
        $this->getType()->shouldReturn('group');
    }
    
    function it_exports_groups_to_csv_file(
        $groupRepository,
        AbstractQuery $query, 
        QueryBuilder $queryBuilder,
        Logger $logger,
        GroupInterface $group,
        GroupInterface $group2,
        Iterator $arrayIterator,
        $iteratorFactory
    ) {
        $groupRepository
            ->createQueryBuilder('g')
            ->willReturn($queryBuilder)
        ;
        
        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;
        
        $this->setConfiguration(array('batch_size' => 1), $logger);
        $group->getId()->willReturn(1);
        $group->getName()->willReturn('Admin');
        $group->getRoles()->willReturn(array('Api_admin', 'Other_admin'));
        $group2->getId()->willReturn(2);
        $group2->getName()->willReturn('User');
        $group2->getRoles()->willReturn(array('User'));
        
        $array = array($group, $group2);
        
        $returnArray = array(
            array(
                'id' => 1,
                'name' => 'Admin',
                'roles' => 'Api_admin~Other_admin'
            )
        );
        
        $returnArray2 = array(
            array(
                'id' => 2,
                'name' => 'User',
                'roles' => 'User'
            )
        );
        
        $query->execute()->willReturn($array);
        $iteratorFactory->createIteratorFromArray($array)->willReturn($arrayIterator);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($group);
        $arrayIterator->next()->shouldBeCalled();
        $group->getId()->shouldBeCalled();
        $group->getName()->shouldBeCalled();
        $group->getRoles()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($group2);
        $arrayIterator->next()->shouldBeCalled();
        $group2->getId()->shouldBeCalled();
        $group2->getName()->shouldBeCalled();
        $group2->getRoles()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray2);
        
        $arrayIterator->valid()->willReturn(false);
        $this->read()->shouldReturn(null);
    }
    
    function it_exports_groups_to_csv_file_if_batch_size_is_greater_than_1(
        $groupRepository,
        AbstractQuery $query, 
        QueryBuilder $queryBuilder,
        Logger $logger,
        GroupInterface $group,
        GroupInterface $group2,
        Iterator $arrayIterator,
        $iteratorFactory
    ) {
        $groupRepository
            ->createQueryBuilder('g')
            ->willReturn($queryBuilder)
        ;
        
        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;
        
        $this->setConfiguration(array('batch_size' => 2), $logger);
        $group->getId()->willReturn(1);
        $group->getName()->willReturn('Admin');
        $group->getRoles()->willReturn(array('Api_admin', 'Other_admin'));
        $group2->getId()->willReturn(2);
        $group2->getName()->willReturn('User');
        $group2->getRoles()->willReturn(array('User'));
        
        $array = array($group, $group2);
        
        $returnArray = array(
            array(
                'id' => 1,
                'name' => 'Admin',
                'roles' => 'Api_admin~Other_admin'
            ),
            array(
                'id' => 2,
                'name' => 'User',
                'roles' => 'User'
            )
        );
        
        $query->execute()->willReturn($array);
        $iteratorFactory->createIteratorFromArray($array)->willReturn($arrayIterator);
        
        $arrayIterator->valid()->willReturn(true, true);
        $arrayIterator->current()->willReturn($group, $group2);
        $arrayIterator->next()->shouldBeCalled();
        $group->getId()->shouldBeCalled();
        $group->getName()->shouldBeCalled();
        $group->getRoles()->shouldBeCalled();
        $group2->getId()->shouldBeCalled();
        $group2->getName()->shouldBeCalled();
        $group2->getRoles()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray);
        
        $arrayIterator->valid()->willReturn(false);
        $this->read()->shouldReturn(null);
    }
}
