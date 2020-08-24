<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\Fixture;

$emotions = [
    'filePath' => [
        new Error(5, 'error message'), // keep this in line
    ],
    'anotherFilePath' => 'Hou'
];
