<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\Fixture;

$emotions = [
    1 => 'Happy', // comment
    2 => 'Relaxed', // comment
    3 => 'Extatic', /* comment */
    /* comment */
    4 => 'Sexy',
    // comment
    5 => 'Finished',
];
