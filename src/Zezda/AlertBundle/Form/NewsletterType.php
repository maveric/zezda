<?php

namespace Zezda\AlertBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsletterType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name')
			->add('site')
			->add('affiliateId')
			->add('guruName')
			->add('types')
			->add('tracked_types')
			->add("smsPrefix")
			->add('isProfitly', NULL, array(
				'required' => FALSE,
			))
			->add('save', 'submit');
	}

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Zezda\AlertBundle\Entity\Newsletter'
		));
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'zezda_alertbundle_newsletter';
	}
}
