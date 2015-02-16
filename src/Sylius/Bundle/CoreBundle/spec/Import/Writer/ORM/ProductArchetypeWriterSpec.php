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
use Sylius\Component\Product\Model\Archetype;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\TranslatableEntityRepository;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class ProductArchetypeWriterSpec extends ObjectBehavior
{
    function let(
        TranslatableEntityRepository $productArchetypeRepository, 
        TranslatableEntityRepository $productAttributeRepository, 
        TranslatableEntityRepository $productOptionRepository, 
        EntityManager $em, 
        Logger $logger
    ) {
        $configuration = array('update' => 1);
        
        $this->beConstructedWith($productArchetypeRepository, $productAttributeRepository, $productOptionRepository, $em);
        $this->setConfiguration($configuration, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\ProductArchetypeWriter');
    }

    function it_is_abstract_doctrine_writer_object()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\AbstractDoctrineWriter');
    }

    function it_implements_writer_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Writer\WriterInterface');
    }

    function it_creates_new_archetype_if_it_does_not_exist(
        $productArchetypeRepository, 
        $productAttributeRepository, 
        $productOptionRepository, 
        Archetype $productArchetype
    ) {
        $data = array(
            'id'         => 1,
            'code'       => 'archetypeCode',
            'name'       => 'testArchetype',
            'parent'     => null,
            'options'    => 'testOption',
            'attributes' => 'testAttribute',
            'createdAt'  => '2015-02-10 10:02:09',
        );

        $productArchetypeRepository->findOneBy(array('code' => 'archetypeCode'))->willReturn(null);
        $productArchetypeRepository->createNew()->willReturn($productArchetype);

        $productArchetype->setCode('archetypeCode');
        $productArchetype->setName('testArchetype');
        $productArchetype->setParent(null);
        $baseAttribute = $productAttributeRepository->findOneBy(array('name' => 'testAttribute'));
        $productArchetype->addAttribute($baseAttribute);
        $baseOption = $productOptionRepository->findOneBy(array('name' => 'testOption'));
        $productArchetype->addOption($baseOption);
        $productArchetype->setCreatedAt('2015-02-10 10:02:09');

        $this->process($data)->shouldReturn($productArchetype);
    }
//
//    function it_updates_archetype_if_it_exists(
//        $productArchetypeRepository, 
//        $productAttributeRepository, 
//        $productOptionRepository, 
//        Archetype $productArchetype
//    ) {
//        $data = array(
//            'id'         => 1,
//            'code'       => 'archetypeCode',
//            'name'       => 'testArchetype',
//            'parent'     => null,
//            'options'    => 'testOption',
//            'attributes' => 'testAttribute',
//            'createdAt'  => '2015-02-10 10:02:09',
//        );
//
//        $productArchetypeRepository->findOneBy(array('code' => 'archetypeCode'))->willReturn($productArchetype);
//        $productArchetypeRepository->createNew()->shouldNotBeCalled();
//        
//        $productArchetype->setCode('archetypeCode');
//        $productArchetype->setName('testArchetype');
//        $productArchetype->setParent(null);
//        $baseAttribute = $productAttributeRepository->findOneBy(array('name' => 'testAttribute'));
//        $productArchetype->addAttribute($baseAttribute);
//        $baseOption = $productOptionRepository->findOneBy(array('name' => 'testOption'));
//        $productArchetype->addOption($baseOption);
//        $productArchetype->setCreatedAt('2015-02-10 10:02:09');
//
//        $this->process($data)->shouldReturn($productArchetype);
//    }
    
    function it_has_type()
    {
        $this->getType()->shouldReturn('product_archetype');
    }
}
