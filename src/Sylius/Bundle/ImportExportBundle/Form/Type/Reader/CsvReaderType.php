<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Form\Type\Reader;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Reader choice choice type.
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CsvReaderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delimiter', 'text', array(
                'label'    => 'sylius.form.reader.csv.delimiter',
                'empty_data'     => ';',
            ))
            ->add('enclosure', 'text', array(
                'label'    => 'sylius.form.reader.csv.enclosure',
                'empty_data'     => '"',
            ))
            ->add('batch', 'text', array(
                'label'    => 'sylius.form.reader.csv.batch_size',
                'empty_data'     => '100',
            ))
            ->add('file', 'text', array(
                'label'    => 'sylius.form.reader.file',
                'required' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_csv_reader';
    }
}