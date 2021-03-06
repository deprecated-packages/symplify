<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\FixturePhp80;

class SomeClass
{
    public function someMethod()
    {
        return ['first' => [
                '$controllerClass' => 'SymfonyController'
        ]];
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\FixturePhp80;

class SomeClass
{
    public function someMethod()
    {
        return [
            'first' => [
                '$controllerClass' => 'SymfonyController'
            ]
        ];
    }
}

?>
