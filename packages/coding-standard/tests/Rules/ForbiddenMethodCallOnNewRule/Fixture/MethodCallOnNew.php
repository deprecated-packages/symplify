<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodCallOnNewRule\Fixture;

(new DateTime('2020-01-01'))->format('Y-m-d');