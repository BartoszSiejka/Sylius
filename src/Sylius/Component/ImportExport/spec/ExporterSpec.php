<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\ImportExport;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\ImportExport\Model\ExportProfile;
use Sylius\Component\ImportExport\Writer\WriterInterface;
use Sylius\Component\ImportExport\Reader\ReaderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\ImportExport\Model\ExportJobInterface;
use Doctrine\ORM\EntityManager;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ExporterSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $readerRegistry, ServiceRegistryInterface $writerRegistry, RepositoryInterface $exportJobRepository, EntityManager $entityManager)
    {
        $this->beConstructedWith($readerRegistry, $writerRegistry, $exportJobRepository, $entityManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Exporter');
    }

    function it_implements_delegating_exporter_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\ExporterInterface');
    }

    function it_exports_data_with_given_exporter($exportJobRepository, ExportJobInterface $exportJob, $readerRegistry, $writerRegistry, ExportProfile $exportProfile, ReaderInterface $reader, WriterInterface $writer)
    {
        $exportJobRepository->createNew()->willReturn($exportJob);
        $exportProfile->addJob($exportJob)->shouldBeCalled();

        $exportProfile->getReader()->willReturn('doctrine');
        $exportProfile->getReaderConfiguration()->willReturn(array());
        $exportProfile->getWriter()->willReturn('csv');
        $exportProfile->getWriterConfiguration()->willReturn(array());

        $readerRegistry->get('doctrine')->willReturn($reader);
        $reader->setConfiguration(array())->shouldBeCalled();
        $reader->read()->willReturn(array(array('readData')));

        $writerRegistry->get('csv')->willReturn($writer);
        $writer->setConfiguration(array())->shouldBeCalled();

        $writer->write(array('readData'))->shouldBeCalled();

        $this->export($exportProfile);
    }
}