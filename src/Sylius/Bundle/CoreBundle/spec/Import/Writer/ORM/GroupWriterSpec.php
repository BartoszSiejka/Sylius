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
use Sylius\Component\Core\Model\Group;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\GroupRepository;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class GroupWriterSpec extends ObjectBehavior
{
    function let(GroupRepository $groupRepository, EntityManager $em, Logger $logger)
    {
        $configuration = array('update' => 1);
        
        $this->beConstructedWith($groupRepository, $em);
        $this->setConfiguration($configuration, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\GroupWriter');
    }

    function it_is_abstract_doctrine_writer_object()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\AbstractDoctrineWriter');
    }

    function it_implements_writer_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Writer\WriterInterface');
    }

    function it_creates_new_group_if_it_does_not_exist($groupRepository, Group $group)
    {
        $data = array(
            'id' => 1,
            'name' => 'testGroup',
            'roles' => 'admin'
        );
        
        $groupRepository->findOneBy(array('name' => 'testGroup'))->willReturn(null);
        $groupRepository->createNew()->willReturn($group);
        
        $this->process($data);
        
        $group->setName('testGroup')->shouldBeCalled();
        $group->addRole('admin')->ShouldBeCalled();
    }

    function it_updates_group_if_it_exists($groupRepository, Group $group)
    {
        $data = array(
            'id' => 1,
            'name' => 'testGroup',
            'roles' => 'admin'
        );

        $groupRepository->findOneBy(array('name' => 'testGroup'))->willReturn($group);
        $groupRepository->createNew()->shouldNotBeCalled();
        
        $this->process($data);
        
        $group->setName('testGroup')->shouldBeCalled();
        $group->addRole('admin')->ShouldBeCalled();
    }
    
    function it_has_type()
    {
        $this->getType()->shouldReturn('import_group');
    }
}
