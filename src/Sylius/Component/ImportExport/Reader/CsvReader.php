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
            
            $this->validate($this->configuration);

            $this->csvReader = new Reader($this->configuration['file'], 'r', true);
            $this->csvReader->setDelimiter($this->configuration['delimiter']);
            $this->csvReader->setEnclosure($this->configuration['enclosure']);
            $this->running = true;
            $this->metadatas['row'] = 0;
        }

        $data = array();

        for ($i = 0; $i < $this->configuration['batch']; $i++) {
            $row = $this->csvReader->getRow();

            if (false === $row) {
                return empty($data) ? null : $data;
            }

            $data[] = $row;
            $this->metadatas['row']++;
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

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'csv';
    }

    private function validate(array $configuration)
    {
        if (empty($this->configuration['file'])) {
            $this->resultCode = -1;
            throw new \InvalidArgumentException('Cannot read data without file path defined.');
        }
        if (empty($this->configuration['delimiter'])) {
            $this->resultCode = -1;
            throw new \InvalidArgumentException('Cannot read data without delimiter defined.');
        }
        if (empty($this->configuration['enclosure'])) {
            $this->resultCode = -1;
            throw new \InvalidArgumentException('Cannot read data without enclosure defined.');
        }
        if ($this->configuration['enclosure'] === '~' || $this->configuration['delimiter'] === '~') {
            $this->resultCode = -1;
            throw new \InvalidArgumentException('Cannot use tilde like a delimeter or eclosure.');
        }
    }
}
