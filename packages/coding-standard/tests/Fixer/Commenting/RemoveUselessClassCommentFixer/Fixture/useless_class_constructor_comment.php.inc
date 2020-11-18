<?php

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessClassCommentFixer\Fixture;

class SomeClass
{
/**
 * SomeClass constructor.
 */
	public function __construct()
	{

	}
}

class SomeClass
{
/**
 * SomeClass Constructor.
 */
	public function __construct()
	{

	}
}

class SomeClass
{
/**
 * SomeClass Constructor
 */
	public function __construct()
	{

	}
}

class SomeClass
{
// SomeClass constructor
	public function __construct()
	{

	}
}
?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessClassCommentFixer\Fixture;

class SomeClass
{

	public function __construct()
	{

	}
}

class SomeClass
{

	public function __construct()
	{

	}
}

class SomeClass
{

	public function __construct()
	{

	}
}

class SomeClass
{

	public function __construct()
	{

	}
}
?>
