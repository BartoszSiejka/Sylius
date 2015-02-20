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
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Monolog\Logger;
use Iterator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class TaxonomyReaderSpec extends ObjectBehavior
{
    function let(EntityRepository $taxonomyRepository, ArrayIteratorFactoryInterface $iteratorFactory)
    {
        $this->beConstructedWith($taxonomyRepository, $iteratorFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Export\Reader\ORM\TaxonomyReader');
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
        $this->getType()->shouldReturn('taxonomy');
    }
    
    function it_exports_groups_to_csv_file(
        $taxonomyRepository,
        AbstractQuery $query, 
        QueryBuilder $queryBuilder,
        Logger $logger,
        TaxonInterface $taxon,
        TaxonInterface $parent,
        TaxonomyInterface $taxonomy,
        TaxonomyInterface $taxonomy2,
        TaxonInterface $root,
        TaxonInterface $root2,
        TaxonInterface $taxon2,
        TaxonInterface $parent2,
        ArrayCollection $arrayCollection,
        ArrayCollection $arrayCollection2,
        ArrayCollection $arrayCollection3,
        Iterator $arrayIterator,
        $iteratorFactory
    ) {
        $taxonomyRepository
            ->createQueryBuilder('t')
            ->willReturn($queryBuilder)
        ;
        
        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;
        
        $this->setConfiguration(array('batch_size' => 1), $logger);
        $taxon->getId()->willReturn(2);
        $taxon->getName()->willReturn('name');
        $taxon->getSlug()->willReturn('slug');
        $taxon->getPermalink()->willReturn('permalink');
        $taxon->getDescription()->willReturn('description');
        $taxon->getLeft()->willReturn(7);
        $taxon->getRight()->willReturn(9);
        $taxon->getLevel()->willReturn(1);
        $taxon->getParent()->willReturn($parent);
        $parent->getId()->willReturn(1);
        $parent->getName()->willReturn('root_name');
        $taxon->getTaxonomy()->willReturn($taxonomy);
        $taxonomy->getId()->willReturn(10);
        $taxonomy->getName()->willReturn('taxonomyName');
        $taxonomy->getRoot()->willReturn($root);
        $root->getId()->willReturn(1);
        $root->getName()->willReturn('root_name');
        $root->getSlug()->willReturn('root_slug');
        $root->getPermalink()->willReturn('root_permalink');
        $root->getDescription()->willReturn('root_description');
        $root->getLeft()->willReturn(8);
        $root->getRight()->willReturn(11);
        $root->getLevel()->willReturn(0);
        
        $taxon2->getId()->willReturn(3);
        $taxon2->getName()->willReturn('name2');
        $taxon2->getSlug()->willReturn('slug2');
        $taxon2->getPermalink()->willReturn('permalink2');
        $taxon2->getDescription()->willReturn('description2');
        $taxon2->getLeft()->willReturn(8);
        $taxon2->getRight()->willReturn(9);
        $taxon2->getLevel()->willReturn(2);
        $taxon2->getParent()->willReturn($parent2);
        $parent2->getId()->willReturn(2);
        $parent2->getName()->willReturn('name');
        $taxon2->getTaxonomy()->willReturn($taxonomy);
        $taxonomy2->getId()->willReturn(10);
        $taxonomy2->getName()->willReturn('taxonomyName');
        $taxonomy2->getRoot()->willReturn($root);
        $root2->getId()->willReturn(1);
        $root2->getName()->willReturn('root_name');
        $root2->getSlug()->willReturn('root_slug');
        $root2->getPermalink()->willReturn('root_permalink');
        $root2->getDescription()->willReturn('root_description');
        $root2->getLeft()->willReturn(8);
        $root2->getRight()->willReturn(11);
        $root2->getLevel()->willReturn(0);
        
        $array = array($taxonomy);
        
        $returnArray = array(
            array(
                'taxonomy_id'      => 10,
                'taxonomy_name'    => 'taxonomyName',
                'id'               => 2,
                'name'             => 'name',
                'slug'             => 'slug',
                'permalink'        => 'permalink',
                'description'      => 'description',
                'left_tree'        => 7,
                'right_tree'       => 9,
                'tree_level'       => 1,
                'parent_id'        => 1,
                'parent_name'      => 'root_name',
                'root_id'          => 1,
                'root_name'        => 'root_name',
                'root_slug'        => 'root_slug',
                'root_permalink'   => 'root_permalink',
                'root_description' => 'root_description',
                'root_left_tree'   => 8,
                'root_right_tree'  => 11,
                'root_tree_level'  => 0,
            ),
            array(
                'taxonomy_id'      => 10,
                'taxonomy_name'    => 'taxonomyName',
                'id'               => 3,
                'name'             => 'name2',
                'slug'             => 'slug2',
                'permalink'        => 'permalink2',
                'description'      => 'description2',
                'left_tree'        => 8,
                'right_tree'       => 9,
                'tree_level'       => 2,
                'parent_id'        => 2,
                'parent_name'      => 'name',
                'root_id'          => 1,
                'root_name'        => 'root_name',
                'root_slug'        => 'root_slug',
                'root_permalink'   => 'root_permalink',
                'root_description' => 'root_description',
                'root_left_tree'   => 8,
                'root_right_tree'  => 11,
                'root_tree_level'  => 0,
            )
        );
        
        $query->execute()->willReturn($array);
        $iteratorFactory->createIteratorFromArray($array)->willReturn($arrayIterator);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($taxonomy);
        $arrayIterator->next()->shouldBeCalled();
        
        $root->getChildren()->shouldBeCalled()->willReturn($arrayCollection);
        $arrayCollection->toArray()->willReturn(array($taxon));
        $taxon->getChildren()->shouldBeCalled()->willReturn($arrayCollection2);
        $arrayCollection2->toArray()->willReturn(array($taxon2));
        $taxon2->getChildren()->shouldBeCalled()->willReturn($arrayCollection3);
        $arrayCollection3->toArray()->willReturn(array());
        
        $taxon->getTaxonomy()->shouldBeCalled()->willReturn($taxonomy);
        $taxonomy->getId()->shouldBeCalled();
        $taxonomy->getName()->shouldBeCalled();
        $taxonomy->getRoot()->shouldBeCalled()->willReturn($root);
        $root->getId()->shouldBeCalled();
        $root->getName()->shouldBeCalled();
        $root->getSlug()->shouldBeCalled();
        $root->getPermalink()->shouldBeCalled();
        $root->getDescription()->shouldBeCalled();
        $root->getLeft()->shouldBeCalled();
        $root->getRight()->shouldBeCalled();
        $root->getLevel()->shouldBeCalled();
        $taxon->getId()->shouldBeCalled();
        $taxon->getName()->shouldBeCalled();
        $taxon->getSlug()->shouldBeCalled();
        $taxon->getPermalink()->shouldBeCalled();
        $taxon->getDescription()->shouldBeCalled();
        $taxon->getLeft()->shouldBeCalled();
        $taxon->getRight()->shouldBeCalled();
        $taxon->getLevel()->shouldBeCalled();
        $taxon->getParent()->shouldBeCalled();
        $parent->getId()->shouldBeCalled();
        $parent->getName()->shouldBeCalled();
        
        $taxon2->getTaxonomy()->shouldBeCalled();
        $taxonomy2->getId()->shouldNotBeCalled();
        $taxonomy2->getName()->shouldNotBeCalled();
        $taxonomy2->getRoot()->shouldNotBeCalled();
        $root2->getId()->shouldNotBeCalled();
        $root2->getName()->shouldNotBeCalled();
        $root2->getSlug()->shouldNotBeCalled();
        $root2->getPermalink()->shouldNotBeCalled();
        $root2->getDescription()->shouldNotBeCalled();
        $root2->getLeft()->shouldNotBeCalled();
        $root2->getRight()->shouldNotBeCalled();
        $root2->getLevel()->shouldNotBeCalled();
        $taxon2->getId()->shouldBeCalled();
        $taxon2->getName()->shouldBeCalled();
        $taxon2->getSlug()->shouldBeCalled();
        $taxon2->getPermalink()->shouldBeCalled();
        $taxon2->getDescription()->shouldBeCalled();
        $taxon2->getLeft()->shouldBeCalled();
        $taxon2->getRight()->shouldBeCalled();
        $taxon2->getLevel()->shouldBeCalled();
        $taxon2->getParent()->shouldBeCalled();
        $parent2->getId()->shouldBeCalled();
        $parent2->getName()->shouldBeCalled();
        $taxon2->getTaxonomy()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray);
        
        $arrayIterator->valid()->willReturn(false);
        $this->read()->shouldReturn(null);
    }
    
    function it_exports_groups_to_csv_file_with_batch_size_check(
        $taxonomyRepository,
        AbstractQuery $query, 
        QueryBuilder $queryBuilder,
        Logger $logger,
        TaxonInterface $taxon,
        TaxonInterface $parent,
        TaxonomyInterface $taxonomy,
        TaxonomyInterface $taxonomy2,
        TaxonInterface $root,
        TaxonInterface $root2,
        TaxonInterface $taxon2,
        TaxonInterface $parent2,
        ArrayCollection $arrayCollection,
        ArrayCollection $arrayCollection2,
        ArrayCollection $arrayCollection3,
        ArrayCollection $arrayCollection4,
        Iterator $arrayIterator,
        $iteratorFactory
    ) {
        $taxonomyRepository
            ->createQueryBuilder('t')
            ->willReturn($queryBuilder)
        ;
        
        $queryBuilder
            ->getQuery()
            ->willReturn($query)
        ;
        
        $this->setConfiguration(array('batch_size' => 1), $logger);
        $taxon->getId()->willReturn(2);
        $taxon->getName()->willReturn('name');
        $taxon->getSlug()->willReturn('slug');
        $taxon->getPermalink()->willReturn('permalink');
        $taxon->getDescription()->willReturn('description');
        $taxon->getLeft()->willReturn(7);
        $taxon->getRight()->willReturn(9);
        $taxon->getLevel()->willReturn(1);
        $taxon->getParent()->willReturn($parent);
        $parent->getId()->willReturn(1);
        $parent->getName()->willReturn('root_name');
        $taxon->getTaxonomy()->willReturn($taxonomy);
        $taxonomy->getId()->willReturn(10);
        $taxonomy->getName()->willReturn('taxonomyName');
        $taxonomy->getRoot()->willReturn($root);
        $root->getId()->willReturn(1);
        $root->getName()->willReturn('root_name');
        $root->getSlug()->willReturn('root_slug');
        $root->getPermalink()->willReturn('root_permalink');
        $root->getDescription()->willReturn('root_description');
        $root->getLeft()->willReturn(8);
        $root->getRight()->willReturn(11);
        $root->getLevel()->willReturn(0);
        
        $taxon2->getId()->willReturn(3);
        $taxon2->getName()->willReturn('name2');
        $taxon2->getSlug()->willReturn('slug2');
        $taxon2->getPermalink()->willReturn('permalink2');
        $taxon2->getDescription()->willReturn('description2');
        $taxon2->getLeft()->willReturn(8);
        $taxon2->getRight()->willReturn(9);
        $taxon2->getLevel()->willReturn(2);
        $taxon2->getParent()->willReturn($parent2);
        $parent2->getId()->willReturn(4);
        $parent2->getName()->willReturn('root_name2');
        $taxon2->getTaxonomy()->willReturn($taxonomy2);
        $taxonomy2->getId()->willReturn(20);
        $taxonomy2->getName()->willReturn('taxonomyName2');
        $taxonomy2->getRoot()->willReturn($root2);
        $root2->getId()->willReturn(4);
        $root2->getName()->willReturn('root_name2');
        $root2->getSlug()->willReturn('root_slug2');
        $root2->getPermalink()->willReturn('root_permalink2');
        $root2->getDescription()->willReturn('root_description2');
        $root2->getLeft()->willReturn(20);
        $root2->getRight()->willReturn(30);
        $root2->getLevel()->willReturn(0);
        
        $array = array($taxonomy);
        
        $returnArray = array(
            array(
                'taxonomy_id'      => 10,
                'taxonomy_name'    => 'taxonomyName',
                'id'               => 2,
                'name'             => 'name',
                'slug'             => 'slug',
                'permalink'        => 'permalink',
                'description'      => 'description',
                'left_tree'        => 7,
                'right_tree'       => 9,
                'tree_level'       => 1,
                'parent_id'        => 1,
                'parent_name'      => 'root_name',
                'root_id'          => 1,
                'root_name'        => 'root_name',
                'root_slug'        => 'root_slug',
                'root_permalink'   => 'root_permalink',
                'root_description' => 'root_description',
                'root_left_tree'   => 8,
                'root_right_tree'  => 11,
                'root_tree_level'  => 0,
            ),
        );
        
        $returnArray2 = array(
            array(
                'taxonomy_id'      => 20,
                'taxonomy_name'    => 'taxonomyName2',
                'id'               => 3,
                'name'             => 'name2',
                'slug'             => 'slug2',
                'permalink'        => 'permalink2',
                'description'      => 'description2',
                'left_tree'        => 8,
                'right_tree'       => 9,
                'tree_level'       => 2,
                'parent_id'        => 4,
                'parent_name'      => 'root_name2',
                'root_id'          => 4,
                'root_name'        => 'root_name2',
                'root_slug'        => 'root_slug2',
                'root_permalink'   => 'root_permalink2',
                'root_description' => 'root_description2',
                'root_left_tree'   => 20,
                'root_right_tree'  => 30,
                'root_tree_level'  => 0,
            )
        );
        
        $query->execute()->willReturn($array);
        $iteratorFactory->createIteratorFromArray($array)->willReturn($arrayIterator);
        
        $arrayIterator->valid()->willReturn(true);
        $arrayIterator->current()->willReturn($taxonomy, $taxonomy2);
        $arrayIterator->next()->shouldBeCalled();
        
        $root->getChildren()->shouldBeCalled()->willReturn($arrayCollection);
        $arrayCollection->toArray()->willReturn(array($taxon));
        $taxon->getChildren()->shouldBeCalled()->willReturn($arrayCollection2);
        $arrayCollection2->toArray()->willReturn(array());
        
        $taxon->getTaxonomy()->shouldBeCalled()->willReturn($taxonomy);
        $taxonomy->getId()->shouldBeCalled();
        $taxonomy->getName()->shouldBeCalled();
        $taxonomy->getRoot()->shouldBeCalled()->willReturn($root);
        $root->getId()->shouldBeCalled();
        $root->getName()->shouldBeCalled();
        $root->getSlug()->shouldBeCalled();
        $root->getPermalink()->shouldBeCalled();
        $root->getDescription()->shouldBeCalled();
        $root->getLeft()->shouldBeCalled();
        $root->getRight()->shouldBeCalled();
        $root->getLevel()->shouldBeCalled();
        $taxon->getId()->shouldBeCalled();
        $taxon->getName()->shouldBeCalled();
        $taxon->getSlug()->shouldBeCalled();
        $taxon->getPermalink()->shouldBeCalled();
        $taxon->getDescription()->shouldBeCalled();
        $taxon->getLeft()->shouldBeCalled();
        $taxon->getRight()->shouldBeCalled();
        $taxon->getLevel()->shouldBeCalled();
        $taxon->getParent()->shouldBeCalled();
        $parent->getId()->shouldBeCalled();
        $parent->getName()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray);
        
        $root2->getChildren()->shouldBeCalled()->willReturn($arrayCollection3);
        $arrayCollection3->toArray()->willReturn(array($taxon2));
        $taxon2->getChildren()->shouldBeCalled()->willReturn($arrayCollection4);
        $arrayCollection4->toArray()->willReturn(array());
        
        $taxon2->getTaxonomy()->shouldBeCalled();
        $taxonomy2->getId()->shouldBeCalled();
        $taxonomy2->getName()->shouldBeCalled();
        $taxonomy2->getRoot()->shouldBeCalled();
        $root2->getId()->shouldBeCalled();
        $root2->getName()->shouldBeCalled();
        $root2->getSlug()->shouldBeCalled();
        $root2->getPermalink()->shouldBeCalled();
        $root2->getDescription()->shouldBeCalled();
        $root2->getLeft()->shouldBeCalled();
        $root2->getRight()->shouldBeCalled();
        $root2->getLevel()->shouldBeCalled();
        $taxon2->getId()->shouldBeCalled();
        $taxon2->getName()->shouldBeCalled();
        $taxon2->getSlug()->shouldBeCalled();
        $taxon2->getPermalink()->shouldBeCalled();
        $taxon2->getDescription()->shouldBeCalled();
        $taxon2->getLeft()->shouldBeCalled();
        $taxon2->getRight()->shouldBeCalled();
        $taxon2->getLevel()->shouldBeCalled();
        $taxon2->getParent()->shouldBeCalled();
        $parent2->getId()->shouldBeCalled();
        $parent2->getName()->shouldBeCalled();
        $taxon2->getTaxonomy()->shouldBeCalled();
        $this->read()->shouldReturn($returnArray2);
        
        $arrayIterator->valid()->willReturn(false);
        $this->read()->shouldReturn(null);
    }
}
