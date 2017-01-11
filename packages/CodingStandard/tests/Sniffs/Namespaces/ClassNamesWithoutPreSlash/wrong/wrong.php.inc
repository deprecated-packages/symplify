<?php

namespace SomeNamespace;


class SomeClass
{

	public function getNow($service)
	{
		if ($service instanceof \stdClass) {
			return new \DateTime;
		}

		return new \SomeNamespace\SomeClass;
	}

}
