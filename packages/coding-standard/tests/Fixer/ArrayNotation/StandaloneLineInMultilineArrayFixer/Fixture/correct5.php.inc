<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\Fixture;

$defaults = [
    'frequencyNumber' => 1,
    'now' => (new NetteDateTime),
    'yesterday' => (new NetteDateTime)->format(DateTime::DATE_FORMAT),
    'tomorrow' => (new NetteDateTime('now'))->format(DateTime::DATE_FORMAT),
    'createOrganiser' => true,
];
