<?php

namespace Zezda\AlertBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AlertType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('datetime', 'datetime', array(
				'widget' => "single_text",
			))
			->add('price')
			->add('isToOpen', NULL, array(
				'required' => FALSE,
			))
			->add('shares')
			->add('type')
			->add('content', 'textarea')
			->add('newsletter')
			->add('stock')
			->add('approved')
			->add('save', 'submit');
	}

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Zezda\AlertBundle\Entity\Alert'
		));
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'alert';
	}
}
