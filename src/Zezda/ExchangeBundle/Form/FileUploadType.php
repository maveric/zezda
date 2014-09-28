<?php

namespace Zezda\ExchangeBundle\Form;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FileUploadType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('attachment', 'file')
			->add('location', 'choice', array(
				'choices'  => array(
					'bad'                                 => 'Pick One',
					'/home/zezda/projects/zezda/candles/' => 'Candles',
					'/home/zezda/projects/zezda/uploads/' => 'Uploads',
				),
				'required' => TRUE,
			))
			->add('save', 'submit');

	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'uploade';
	}
}
