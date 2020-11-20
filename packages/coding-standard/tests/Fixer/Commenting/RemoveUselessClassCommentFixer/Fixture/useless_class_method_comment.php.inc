<?php

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessClassCommentFixer\Fixture;

class SomeClass
{
/**
 * Get Translator
 */
	public function getTranslator()
	{

	}

/**
 * Set Translator
 */
	public function setTranslator()
	{

	}
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessClassCommentFixer\Fixture;

class SomeClass
{

	public function getTranslator()
	{

	}


	public function setTranslator()
	{

	}
}

?>
