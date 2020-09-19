<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixerTest\Fixture;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;

class SkipDoubleOpener
{
    public function getErrors($service)
    {
        $services->set(ArraySyntaxFixer::class)
            ->call('configure', [[
                'syntax' => 'short',
            ]]);
    }
}
