<?php

namespace SomeNamespace;

use DateTime;
use SomeOtherClass;
use stdClass;


class SomeClass
{

	public function getNow($service)
	{
		if ($service instanceof stdClass) {
			return new DateTime;
		}
		return new SomeOtherClass;
	}

}
