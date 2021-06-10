<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMaskWithoutSprintfRule\Fixture;

$value = <<<'CODE_SAMPLE'
'Hey %s';
CODE_SAMPLE;
