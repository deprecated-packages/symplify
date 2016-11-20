<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ControllerAutowire\Controller\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilder;

trait ControllerFormTrait
{
	/**
	 * @var FormFactoryInterface
	 */
	private $formFactory;

	public function setFormFactory(FormFactoryInterface $formFactory)
	{
		$this->formFactory = $formFactory;
	}

	/**
	 * Creates and returns a Form instance from the type of the form.
	 *
	 * @param string $type    The fully qualified class name of the form type
	 * @param mixed  $data    The initial data for the form
	 * @param array  $options Options for the form
	 */
	protected function createForm(string $type, $data = null, array $options = array()) : FormInterface
	{
		return $this->formFactory->create($type, $data, $options);
	}

	/**
	 * @param mixed $data    The initial data for the form
	 * @param array $options Options for the form
	 */
	protected function createFormBuilder($data = null, array $options = array()) : FormBuilder
	{
		return $this->formFactory->createBuilder(FormType::class, $data, $options);
	}
}
