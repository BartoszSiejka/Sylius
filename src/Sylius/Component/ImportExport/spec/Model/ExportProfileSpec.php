<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\ImportExport\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ExportProfileSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Model\ExportProfile');
    }

    public function it_is_profile_object()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Model\Profile');
    }

    public function it_has_exporter()
    {
        $this->setExporter('testExporter');
        $this->getExporter()->shouldReturn('testExporter');
    }

    public function it_has_exporter_configuration()
    {
        $exporterConfiguration = array('config1' => 'First field of configuration', 'config2' => 'Second field of configuration');
        $this->setExporterConfiguration($exporterConfiguration);
        $this->getExporterConfiguration()->shouldReturn($exporterConfiguration);
    }
}