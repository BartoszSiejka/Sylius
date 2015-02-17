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
use Sylius\Component\ImportExport\Reader\ReaderInterface;

/**
 * Export reader.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
abstract class AbstractDoctrineReader implements ReaderInterface
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
    
    abstract public function getQuery();

    abstract public function process($result);

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $results = array();

        if (!$this->running) {
            $this->running = true;
            $this->results = new \ArrayIterator($this->getQuery()->execute());
            $this->batchSize = $this->configuration['batch_size'];
            $this->statistics = array();
            $this->statistics['row'] = 0;
        }

        for ($i = 0; $i<$this->batchSize; $i++) {
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

            $result = $this->process($result);
            $results[] = $result;
            $this->statistics['row']++;
        }
        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(array $configuration, Logger $logger)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function finalize(JobInterface $job)
    {
        $this->statistics['result_code'] = $this->resultCode;
        $job->addMetadata('reader', $this->statistics);
    }

    /**
     * {@inheritdoc}
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }
}
