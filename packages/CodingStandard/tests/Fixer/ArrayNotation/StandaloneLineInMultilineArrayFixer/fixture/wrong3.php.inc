<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\Wrong;

class SomeClass
{
    public function someMethod()
    {
        return ['first' => [
                '$controllerClass' => SymfonyController::class
        ]];
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\Wrong;

class SomeClass
{
    public function someMethod()
    {
        return [
            'first' => [
            '$controllerClass' => SymfonyController::class
        ]
        ];
    }
}

?>
