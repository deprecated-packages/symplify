<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\Fixture;

class SomeClass
{
    public function getErrors()
    {
        $error = [
            'line' => $this->wrapMessageToStyle((string) $error->getLine(), $error->isFixable()),
            'message' => $this->wrapMessageToStyle($message, $error->isFixable()),
        ];
    }
}
