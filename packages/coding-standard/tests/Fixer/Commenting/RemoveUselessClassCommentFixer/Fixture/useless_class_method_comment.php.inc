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
}

?>
