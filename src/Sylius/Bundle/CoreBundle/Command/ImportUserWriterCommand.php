<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
* @author Bartosz Siejka <bartosz.siejka@lakion.com>
*/
class ImportUserWriterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:import:writer:user')
            ->setDescription('Test command for import writer class.')
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
        $userWriter = $this->getContainer()->get('sylius.repository.user');
        
        if ($importProfile === null) {
            throw new \InvalidArgumentException('There is no export profile with given code.');
        }

        $this->getContainer()->get('sylius.import_export.importer')->import($importProfile);
        
        $userWriter->write();
    }
}