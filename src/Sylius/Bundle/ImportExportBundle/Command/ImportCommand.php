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

/**
* @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
*/
class ImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:import')
            ->setDescription('Command for exporting data based on export profile.')
            ->addArgument(
                'code',
                InputArgument::REQUIRED,
                'Code of import profile, which data will be imported.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importProfile = $this->getContainer()->get('sylius.repository.import_profile')->findOneByCode($input->getArgument('code'));
        if ($importProfile === null) {
            throw new \InvalidArgumentException('There is no import profile with given code.');
        }

        $this->getContainer()->get('sylius.import_export.importer')->import($importProfile);
    }
}
