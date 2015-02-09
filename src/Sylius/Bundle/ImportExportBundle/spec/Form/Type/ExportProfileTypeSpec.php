<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ImportExportBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Prophecy\Argument;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\ImportExport\Reader\ReaderInterface;
use Sylius\Component\ImportExport\Writer\WriterInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ExportProfileTypeSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $readerRegistry, ServiceRegistryInterface $writerRegistry)
    {
        $this->beConstructedWith('Sylius\Component\ImportExport\Model\ExportProfile', array('sylius'), $readerRegistry, $writerRegistry);
    }   

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ImportExportBundle\Form\Type\ExportProfileType');
    }

    function it_should_be_abstract_resource_type_object()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType');
    }

    function it_build_form_with_proper_fields(
        FormBuilderInterface $builder,
        FormFactoryInterface $factory,
        $readerRegistry,
        $writerRegistry,
        ReaderInterface $reader,
        WriterInterface $writer,
        FormInterface $readerForm,
        FormInterface $writerForm
    ) {
        $builder->getFormFactory()->willReturn($factory);

        $builder->addEventSubscriber(Argument::type('Sylius\Bundle\ImportExportBundle\Form\EventListener\BuildWriterFormListener'))->shouldBeCalled()->willReturn($builder);
        $builder->addEventSubscriber(Argument::type('Sylius\Bundle\ImportExportBundle\Form\EventListener\BuildReaderFormListener'))->shouldBeCalled()->willReturn($builder);
        $builder->add('name', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('code', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('description', 'textarea', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('reader', 'sylius_reader_choice', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('writer', 'sylius_writer_choice', Argument::any())->shouldBeCalled()->willReturn($builder);

        $reader->getType()->willReturn('testReader');
        $writer->getType()->willReturn('testWriter');
        $readerRegistry->all()->willReturn(array($reader));
        $writerRegistry->all()->willReturn(array($writer));

        $builder->create('readerConfiguration', 'sylius_testReader_reader')->shouldBeCalled()->willReturn($builder);
        $builder->getForm()->shouldBeCalled()->willReturn($readerForm);
        $builder->create('writerConfiguration', 'sylius_testWriter_writer')->shouldBeCalled()->willReturn($builder);
        $builder->getForm()->shouldBeCalled()->willReturn($writerForm);

        $prototypes = array('reader' => array($readerForm), 'writer' => array($writerForm));
        $builder->setAttribute('prototypes', $prototypes)->shouldBeCalled();

        $this->buildForm($builder, array());
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_export_profile');
    }
}