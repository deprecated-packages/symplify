<?php

namespace SomeNamespace;


class SomeClass
{

	/**
	 * @var int
	 */
	public const COUNT = 5;


	public function getSome($count = self::COUNT)
	{
		self::COUNT;
	}

}
