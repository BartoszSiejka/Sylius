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
use Doctrine\ORM\EntityManager;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\ImportExport\Model\Job;
use Sylius\Component\ImportExport\Model\JobInterface;
use Monolog\Logger;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class Importer extends JobRunner implements ImporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        ServiceRegistryInterface $readerRegistry, 
        ServiceRegistryInterface $writerRegistry,
        RepositoryInterface $importJobRepository,
        EntityManager $entityManager,
        Logger $logger) {
        parent::__construct($readerRegistry, $writerRegistry, $importJobRepository, $entityManager, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function import(ImportProfileInterface $importProfile)
    {
        $job = $this->startJob($importProfile);

        if (null === $readerType = $importProfile->getReader()) {
            $this->logger->error(sprintf('ImportProfile: %d. Cannot read data with ImportProfile instance without reader defined.', $importProfile->getId()));
            throw new \InvalidArgumentException('Cannot read data with ImportProfile instance without reader defined.');
        }
        if (null === $writerType = $importProfile->getWriter()) {
            $this->logger->error(sprintf('ImportProfile: %d. Cannot read data with ImportProfile instance without reader defined.', $importProfile->getId()));
            throw new \InvalidArgumentException('Cannot write data with ImportProfile instance without writer defined.');
        }

        $reader = $this->readerRegistry->get($readerType);
        $reader->setConfiguration($importProfile->getReaderConfiguration(), $this->logger);

        $writer = $this->writerRegistry->get($writerType);
        $writer->setConfiguration($importProfile->getWriterConfiguration(), $this->logger);


        while (null !== ($readedLine = $reader->read())) {
            $writer->write($readedLine);
        }

        $this->endJob($job);
    }
}