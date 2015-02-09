<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Form\Type\Writer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Excel writer type
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ExcelWriterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('add_header', 'checkbox', array(
                'label'    => 'sylius.form.writer.csv.add_header',
                'required' => false,
            ))
            ->add('file', 'text', array(
                'label'    => 'sylius.form.writer.file',
                'required' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_excel_writer';
    }
}