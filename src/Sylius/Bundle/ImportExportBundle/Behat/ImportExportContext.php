<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;

/**
 * ImportExportContext for ImportExport scenarios
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ImportExportContext extends DefaultContext
{
    /**
     * @Given there are following export profiles configured:
     * @And there are following export profiles configured:
     */
    public function thereAreExportProfiles(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('export_profile');

        foreach ($table->getHash() as $data) {
            $this->thereIsExportProfile($data['name'], $data['description'], $data["code"], $data['reader'], $data['reader configuration'], $data['writer'], $data['writer configuration'], false);
        }

        $manager->flush();
    }
    
    public function thereIsExportProfile($name, $description, $code, $reader, $readerConfiguration, $writer, $writerConfiguration, $flush = true)
    {
        $repository = $this->getRepository('export_profile');
        $exportProfile = $repository->createNew();
        $exportProfile->setName($name);
        $exportProfile->setDescription($description);
        $exportProfile->setCode($code);
        
        $exportProfile->setReader($reader);
        $exportProfile->setReaderConfiguration($this->getConfiguration($readerConfiguration));

        $writerConfiguration = $this->getConfiguration($writerConfiguration);
        $writerConfiguration["add_headers"] = isset($writerConfiguration["add_headers"]) ? false : true;

        $exportProfile->setWriter($writer);
        $exportProfile->setWriterConfiguration($writerConfiguration);

        $menager = $this->getEntityManager();
        $menager->persist($exportProfile);

        if ($flush) {
            $menager->flush();
        }
        
        return $exportProfile;
    }
}