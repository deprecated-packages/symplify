<?php

namespace SomeNamespace;


class SomeClass
{

	public function run()
	{
		/** @var Presenter $presenter */
		$presenter = $di->getService('IPresenter');
	}

}
