<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Writer;

use EasyCSV\Writer;
use Monolog\Logger;
use Gaufrette\Filesystem;
use Sylius\Component\ImportExport\Model\JobInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CsvWriter implements WriterInterface
{
    /**
     * Is EasySCV\Writer initialized
     *
     * @var boolean
     */
    private $running = false;

    /**
     * @var Writer
     */
    private $csvWriter;

    /**
     * @var array
     */
    private $configuration;

    /**
     * @var boolean
     */
    private $isHeaderSet = false;

    /**
     * Work logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var int
     */
    private $resultCode = 0;

    /**
     * @var array
     */
    private $metadatas = array();

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param array $items
     */
    public function write(array $items)
    {
        if (!$this->running) {
            $this->csvWriter = new Writer($this->configuration['file'], 'w');
            $this->csvWriter->setDelimiter($this->configuration['delimiter']);
            $this->csvWriter->setEnclosure($this->configuration['enclosure']);
            $this->running = true;
            $this->metadatas['row'] = 0;
        }

        if (!$this->isHeaderSet) {
            $header = array_keys($items);
            $this->csvWriter->writeRow($header);
            $this->isHeaderSet = true;
        }

        $this->csvWriter->writeFromArray($items);
        $this->metadatas['row']++;
    }

    /**
     * {@inheritdoc}
     */
    public function finalize(JobInterface $job)
    {
        $fileName = sprintf('export_%d_%s.csv', $job->getProfile()->getId(), $job->getStartTime()->format('Y_m_d_H_i_s'));
        $this->filesystem->write($fileName, file_get_contents($this->configuration['file']));
        $job->setFilePath($fileName);
        $this->metadatas['result_code'] = $this->resultCode;
        $job->addMetadata('writer',$this->metadatas);
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
    public function setConfiguration(array $configuration, Logger $logger)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'csv';
    }
}
