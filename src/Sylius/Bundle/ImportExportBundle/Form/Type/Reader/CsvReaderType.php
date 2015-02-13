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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                'constraints' => array(
                    new NotBlank(array('groups' => array('sylius'))),
                    new Length(array('groups' => array('sylius'), 'min' => 1, 'max' => 1)),
                ),
            ))
            ->add('enclosure', 'text', array(
                'label'    => 'sylius.form.reader.csv.enclosure',
                'constraints' => array(
                    new NotBlank(array('groups' => array('sylius'))),
                    new Length(array('groups' => array('sylius'), 'min' => 1, 'max' => 1)),
                ),
            ))
            ->add('batch', 'number', array(
                'label'    => 'sylius.form.reader.csv.batch_size',
                'required' => true,
                'constraints' => array(
                    new NotBlank(array('groups' => array('sylius'))),
                ),
            ))
            ->add('file', 'text', array(
                'label'    => 'sylius.form.writer.file',
                'required' => true,
                'constraints' => array(
                    new NotBlank(array('groups' => array('sylius'))),
                ),
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
