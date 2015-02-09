<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport;

use Sylius\Component\ImportExport\Model\ImportProfileInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class Importer implements ImporterInterface
{
    /**
     * Importer registry
     *
     * @var ServiceRegistryInterface
     */
    private $readerRegistry;

    /**
     * Importer registry
     *
     * @var ServiceRegistryInterface
     */
    private $writerRegistry;

    /**
     * Constructor
     *
     * @var ServiceRegistryInterface $readerRegistry
     * @var ServiceRegistryInterface $writerRegistry
     */
    public function __construct(ServiceRegistryInterface $readerRegistry, ServiceRegistryInterface $writerRegistry)
    {
        $this->readerRegistry = $readerRegistry;
        $this->writerRegistry = $writerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function import(ImportProfileInterface $importProfile)
    {
        if (null === $readerType = $importProfile->getReader()) {
            throw new \InvalidArgumentException('Cannot read data with ImportProfile instance without reader defined.');
        }
        if (null === $writerType = $importProfile->getWriter()) {
            throw new \InvalidArgumentException('Cannot write data with ImportProfile instance without writer defined.');
        }

        $reader = $this->readerRegistry->get($readerType);
        $reader->setConfiguration($importProfile->getReaderConfiguration());

        $writer = $this->writerRegistry->get($writerType);
        $writer->setConfiguration($importProfile->getWriterConfiguration());

        while (null !== $data = $reader->read()) {     
            $writer->write($data);
        }
    }
}