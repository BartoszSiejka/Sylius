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
use Sylius\Component\Taxonomy\Model\Taxon;
use Sylius\Component\Taxonomy\Model\Taxonomy;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\TranslatableEntityRepository;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class TaxonomyWriterSpec extends ObjectBehavior
{
    function let(TranslatableEntityRepository $taxonomyRepository, TaxonRepository $taxonRepository, EntityManager $em, Logger $logger)
    {
        $configuration = array('update' => 1);
        
        $this->beConstructedWith($taxonomyRepository, $taxonRepository, $em);
        $this->setConfiguration($configuration, $logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\TaxonomyWriter');
    }

    function it_is_abstract_doctrine_writer_object()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\AbstractDoctrineWriter');
    }

    function it_implements_writer_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Writer\WriterInterface');
    }
    
//    function it_creates_new_option_if_it_does_not_exist($taxonomyRepository, $taxonRepository, Taxon $taxon, Taxon $root, Taxon $parentTaxon, Taxonomy $taxonomy)
//    {
//        $data = array(array(
//            'taxonomy_id'      => 1,
//            'taxonomy_name'    => 'taxonomyName',
//            'root_id'          => 2,
//            'root_name'        => 'rootName',
//            'root_slug'        => 'rootSlug',
//            'root_permalink'   => 'rootPermalink',
//            'root_description' => 'rootDescription',
//            'root_left_tree'   => 'rootLeftTree',
//            'root_right_tree'  => 'rootRightTree',
//            'root_tree_level'  => 'rootTreeLevel',
//            'id'               => 3,
//            'name'             => 'name',
//            'slug'             => 'slug',
//            'permalink'        => 'permalink',
//            'description'      => 'description',
//            'left_tree'        => 'leftTree',
//            'right_tree'       => 'rightTree',
//            'tree_level'       => 'treeLevel',
//            'parent_id'        => 4,
//            'parent_name'      => 'parentName',
//        ));
//
//        $taxonomyRepository->findOneBy(array('name' => 'taxonomyName'))->willReturn(null);
//        $taxonomyRepository->createNew()->willReturn($taxonomy);
//
//        $taxonRepository->findOneBy(array('id' => 4))->willReturn($parentTaxon);
//        
//        $taxonomy->setName('taxonomyName')->shouldBeCalled();
//        $taxon->setTaxonomy($taxonomy)->shouldBeCalled();
//        $root->setName('rootName')->shouldBeCalled();
//        $root->setSlug('rootSlug')->shouldBeCalled();
//        $root->setPermalink('rootPermalink')->shouldBeCalled();
//        $root->setDescription('rootDescription')->shouldBeCalled();
//        $root->setLeft('rootLeftTree')->shouldBeCalled();
//        $root->setRight('rootRightTree')->shouldBeCalled();
//        $taxonomy->setRoot($root)->shouldBeCalled();
//        
//        $taxonRepository->createNew()->willReturn($root);
//        
//        $taxon->setName('name')->shouldBeCalled();
//        $taxon->setSlug('slug')->shouldBeCalled();
//        $taxon->setPermalink('permalink')->shouldBeCalled();
//        $taxon->setDescription('description')->shouldBeCalled();
//        $taxon->setLeft('leftTree')->shouldBeCalled();
//        $taxon->setRight('rightTree')->shouldBeCalled();
//        $taxon->setLevel('treeLevel')->shouldBeCalled();
//        $taxon->setParent($parentTaxon)->shouldBeCalled();
//        $taxonomy->addTaxon($taxon)->shouldBeCalled();
//
//        $this->write($data);
//    }
//
//    function it_updates_option_if_it_exists($taxonomyRepository, $taxonRepository, Taxon $taxon, Taxon $root, Taxon $parentTaxon, Taxonomy $taxonomy)
//    {
//         $data = array(
//            'taxonomy_id'      => 1,
//            'taxonomy_name'    => 'taxonomyName',
//            'root_id'          => 2,
//            'root_name'        => 'rootName',
//            'root_slug'        => 'rootSlug',
//            'root_permalink'   => 'rootPermalink',
//            'root_description' => 'rootDescription',
//            'root_left_tree'   => 'rootLeftTree',
//            'root_right_tree'  => 'rootRightTree',
//            'root_tree_level'  => 'rootTreeLevel',
//            'id'               => 3,
//            'name'             => 'name',
//            'slug'             => 'slug',
//            'permalink'        => 'permalink',
//            'description'      => 'description',
//            'left_tree'        => 'leftTree',
//            'right_tree'       => 'rightTree',
//            'tree_level'       => 'treeLevel',
//            'parent_id'        => 4,
//            'parent_name'      => 'parentName',
//        );
//
//        $taxonomyRepository->findOneBy(array('name' => 'taxonomyName'))->willReturn($taxonomy);
//        $taxonomyRepository->createNew()->shouldNotBeCalled();
//
//        $taxonRepository->createNew()->willReturn($root);
//        $taxonRepository->findOneBy(array('id' => 4))->willReturn($parentTaxon);
//
//        $this->process($data);
//        
//        $taxonomy->setName('taxonomyName')->shouldBeCalled();
//        $taxon->setTaxonomy($taxonomy)->shouldBeCalled();
//        $root->setName('rootName')->shouldBeCalled();
//        $root->setSlug('rootSlug')->shouldBeCalled();
//        $root->setPermalink('rootPermalink')->shouldBeCalled();
//        $root->setDescription('rootDescription')->shouldBeCalled();
//        $root->setLeft('rootLeftTree')->shouldBeCalled();
//        $root->setRight('rootRightTree')->shouldBeCalled();
//        $taxonomy->setRoot($root)->shouldBeCalled();
//        
//        $taxon->setName('name')->shouldBeCalled();
//        $taxon->setSlug('slug')->shouldBeCalled();
//        $taxon->setPermalink('permalink')->shouldBeCalled();
//        $taxon->setDescription('description')->shouldBeCalled();
//        $taxon->setLeft('leftTree')->shouldBeCalled();
//        $taxon->setRight('rightTree')->shouldBeCalled();
//        $taxon->setLevel('treeLevel')->shouldBeCalled();
//        $taxon->setParent($parentTaxon)->shouldBeCalled();
//        $taxonomy->addTaxon($taxon)->shouldBeCalled();
//    }
    
    function it_has_type()
    {
        $this->getType()->shouldReturn('taxonomy');
    }
}
