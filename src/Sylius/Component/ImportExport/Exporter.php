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

use Sylius\Component\ImportExport\Model\ExportProfileInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Doctrine\ORM\EntityManager;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Monolog\Logger;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class Exporter extends JobRunner implements ExporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        ServiceRegistryInterface $readerRegistry,
        ServiceRegistryInterface $writerRegistry,
        RepositoryInterface $exportJobRepository,
        EntityManager $entityManager,
        Logger $logger)
    {
        parent::__construct($readerRegistry, $writerRegistry, $exportJobRepository, $entityManager, $logger);
    }

    public function export(ExportProfileInterface $exportProfile)
    {
        $exportJob = $this->startJob($exportProfile);

        if (null === $readerType = $exportProfile->getReader()) {
            $this->logger->error(sprintf('ExportProfile: %d. Cannot read data with ExportProfile instance without reader defined.', $exportProfile->getId()));
            throw new \InvalidArgumentException('Cannot read data with ExportProfile instance without reader defined.');
        }
        if (null === $writerType = $exportProfile->getWriter()) {
            $this->logger->error(sprintf('ExportProfile: %d. Cannot read data with ExportProfile instance without reader defined.', $exportProfile->getId()));
            throw new \InvalidArgumentException('Cannot write data with ExportProfile instance without writer defined.');
        }

        $reader = $this->readerRegistry->get($readerType);
        $reader->setConfiguration($exportProfile->getReaderConfiguration(), $this->logger);

        $writer = $this->writerRegistry->get($writerType);
        $writer->setConfiguration($exportProfile->getWriterConfiguration(), $this->logger);

        foreach ($reader->read() as $data) {
            $writer->write($data);
        }

        $this->endJob($exportJob);
    }
}
