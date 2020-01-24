<?php

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Fixture;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Tokenizer\Tokens;

class RealFixer implements DefinedFixerInterface
{
    private $property;

    public function secondMethod()
    {

    }

    public function someExtraMethod()
    {

    }

    public function firstMethod()
    {

    }

    public function getDefinition()
    {
    }

    public function isCandidate(Tokens $tokens)
    {
    }

    public function isRisky()
    {
    }

    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
    }

    public function getName()
    {
    }

    public function getPriority()
    {
    }

    public function supports(\SplFileInfo $file)
    {
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Fixture;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Tokenizer\Tokens;

class RealFixer implements DefinedFixerInterface
{
    private $property;

    public function firstMethod()
    {

    }

    public function secondMethod()
    {

    }

    public function getDefinition()
    {
    }

    public function isCandidate(Tokens $tokens)
    {
    }

    public function someExtraMethod()
    {

    }

    public function isRisky()
    {
    }

    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
    }

    public function getName()
    {
    }

    public function getPriority()
    {
    }

    public function supports(\SplFileInfo $file)
    {
    }
}

?>
