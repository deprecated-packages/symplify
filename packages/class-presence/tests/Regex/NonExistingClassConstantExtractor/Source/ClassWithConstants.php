<?php

declare(strict_types=1);

namespace Symplify\ClassPresence\Tests\Regex\NonExistingClassConstantExtractor\Source;

final class ClassWithConstants
{
    public const EXISTING = 'yes';

    public const EXISTING_1000 = 'yes 1000 times';

    public const existing_lower_case = 'small';
}
