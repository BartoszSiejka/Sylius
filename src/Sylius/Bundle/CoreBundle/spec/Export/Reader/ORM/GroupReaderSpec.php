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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class GroupReaderSpec extends ObjectBehavior
{
    function let(EntityRepository $groupRepository)
    {
        $this->beConstructedWith($groupRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Export\Reader\ORM\GroupReader');
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
        $this->getType()->shouldReturn('group');
    }
    
    function it_exports_groups_to_csv_file(
        $groupRepository,
        AbstractQuery $query, 
        QueryBuilder $queryBuilder,
        Logger $logger
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
        
        $array = array(
            array(
                'id' => 1,
                'name' => 'Admin',
                'roles' => array('Api_admin', 'Other_admin')
            ),
            array(
                'id' => 2,
                'name' => 'User',
                'roles' => array('User')
            )
        );
        $query->execute()->willReturn($array);
        
        $this->read()->shouldReturnArray();
    }
}
