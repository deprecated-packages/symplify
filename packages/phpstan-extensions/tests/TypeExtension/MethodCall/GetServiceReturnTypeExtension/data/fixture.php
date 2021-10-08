<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

$currentWorkingDirectory = getcwd();
assertType('string', $currentWorkingDirectory);
