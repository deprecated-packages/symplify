<?php

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessClassCommentFixer\Fixture;

class SomeClass
{
/**
 * Get normal translator
 */
	public function getNormalTranslator()
	{

	}
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessClassCommentFixer\Fixture;

class SomeClass
{

	public function getNormalTranslator()
	{

	}
}

?>
