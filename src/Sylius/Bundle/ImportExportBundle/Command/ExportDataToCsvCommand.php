<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Sylius\Component\ImportExport\Writer\CsvWriter;

/**
* @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
*/
class ExportDataToCsvCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:export')
            ->setDescription('Test command for exporting data to csv file.')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Path to csv file to be saved.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csvWriter = new CsvWriter();
        $csvWriter->write(array('item1' => 'Item1', 'item2' => 'Item2'), array('file' => $input->getArgument('file')));

        $output->writeln(sprintf('File %s has been created.', $input->getArgument('file')));
    }
}