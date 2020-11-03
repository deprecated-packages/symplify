<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallOnNewRule\Fixture;

(new DateTime('2020-01-01'))->format('Y-m-d');
