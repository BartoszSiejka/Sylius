<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Reader;

use EasyCSV\Reader;
use Monolog\Logger;
use Sylius\Component\ImportExport\Model\JobInterface;

/**
 * Csv import reader
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CsvReader implements ReaderInterface
{
    /**
     * Is EasySCV\Reader initialized
     *
     * @var boolean
     */
    private $running = false;

    /**
     * @var Reader
     */
    private $csvReader;

    /**
     * @var array
     */
    private $configuration;

    /**
     * Work logger
     *
     * @var Logger
     */
    protected $logger;
    
    /**
     * @var int
     */
    private $resultCode = 0;

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (!$this->running) {
            $this->csvReader = new Reader($this->configuration['file'], 'r', true);
            $this->csvReader->setDelimiter($this->configuration['delimiter']);
            $this->csvReader->setEnclosure($this->configuration['enclosure']);
            $this->running = true;
        }

        $data = array();

        for ($i = 0; $i < $this->configuration['batch']; $i++) {
            $row = $this->csvReader->getRow();

            if (false === $row) {
                return empty($data) ? null : $data;
            }

            $data[] = $row;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(array $configuration, Logger $logger)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    public function finalize(JobInterface $job)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'csv';
    }
}
