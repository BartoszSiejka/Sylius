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
    private $results;
    private $running = false;
    private $configuration;
    private $logger;
    
    /**
     * @var int
     */
    private $resultCode = 0;

    public function read()
    {
        if (!$this->running) {
            $this->running = true;
            $this->results = $this->getQuery()->execute();
            $this->results = new \ArrayIterator($this->results);
            $batchSize = $this->configuration['batch_size'];
            $this->metadatas['row'] = 0;
        }

        $results = array();

        for ($i = 0; $i<$batchSize; $i++) {
            if ($result = $this->results->current()) {
                $this->results->next();
            }

            $result = $this->process($result);
            $results[] = $result;
            $this->metadatas['row']++;
        }

        return $results;
    }

    public function setConfiguration(array $configuration, Logger $logger)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    public abstract function process($result);

    /**
     * {@inheritdoc}
     */
    public function finalize(JobInterface $job)
    {
        $this->metadatas['result_code'] = $this->resultCode;
        $job->addMetadata('reader',$this->metadatas);
    }

    /**
     * {@inheritdoc}
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }
}
