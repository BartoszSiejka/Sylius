<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Export\Reader\ORM;

use Monolog\Logger;
use Sylius\Component\ImportExport\Model\JobInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Component\ImportExport\Factory\ArrayIteratorFactoryInterface;
use Sylius\Component\Core\Model\TaxonInterface;

/**
 * Export taxonomy reader.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class TaxonomyReader extends AbstractDoctrineReader
{
    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var int
     */
    protected $resultCode = 0;

    /**
     * Batch size
     *
     * @var integer
     */
    protected $batchSize;

    /**
     * @var array
     */
    private $results;

    /**
     * @var bool
     */
    private $running = false;

    /**
     * @var array
     */
    private $statistics;
    
    /**
     * @param ArrayIteratorFactoryInterface $iteratorFactory
     */
    private $iteratorFactory;

    public function __construct(EntityRepository $taxonomyRepository, ArrayIteratorFactoryInterface $iteratorFactory)
    {
        $this->taxonomyRepository = $taxonomyRepository;
        $this->iteratorFactory = $iteratorFactory;
    }

    public function read()
    {
        $results = array();
        $taxons = array();

        if (!$this->running) {
            $this->running = true;
            $this->results = $this->iteratorFactory->createIteratorFromArray($this->getQuery()->execute());
            $this->batchSize = $this->configuration['batch_size'];
            $this->statistics = array();
            $this->statistics['row'] = 0;
        }

        for ($i = 0; $i < $this->batchSize; $i++) {
            if (false === $this->results->valid()) {
                if (empty($results)) { 
                    $this->running = false;
                    
                    return null;
                }
                
                return $results;
            }
            
            if ($result = $this->results->current()) {
                $this->results->next();
            }
            
            $taxons = array_merge($taxons, $this->getChildren($result->getRoot()));
            $results = $this->process($taxons);
        }
        
        return $results;
    }

    public function getQuery()
    {
        $query = $this->taxonomyRepository->createQueryBuilder('t')
                ->getQuery();

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'taxonomy';
    }
    
    protected function process($taxons) {
        $results = array();
        
        foreach ($taxons as $taxon) { 
            $taxonomy = $taxon->getTaxonomy();
            $root = $taxonomy->getRoot();
            
            $results[] = array(
                'taxonomy_id'      => $taxonomy->getId(),
                'taxonomy_name'    => $taxonomy->getName(),
                'id'               => $taxon->getId(),
                'name'             => $taxon->getName(),
                'slug'             => $taxon->getSlug(),
                'permalink'        => $taxon->getPermalink(),
                'description'      => $taxon->getDescription(),
                'left_tree'        => $taxon->getLeft(),
                'right_tree'       => $taxon->getRight(),
                'tree_level'       => $taxon->getLevel(),
                'parent_id'        => $taxon->getParent()->getId(),
                'parent_name'      => $taxon->getParent()->getName(),
                'root_id'          => $root->getId(),
                'root_name'        => $root->getName(),
                'root_slug'        => $root->getSlug(),
                'root_permalink'   => $root->getPermalink(),
                'root_description' => $root->getDescription(),
                'root_left_tree'   => $root->getLeft(),
                'root_right_tree'  => $root->getRight(),
                'root_tree_level'  => $root->getLevel(),
            );
            
            $this->statistics['row']++;
        }
        
        return $results;
    }

    public function getChildren(TaxonInterface $taxon)
    {
        $children = $taxon->getChildren()->toArray();

        foreach ($children as $child) {
            $children = array_merge($children, $this->getChildren($child));
        }

        return $children;
    }
}
