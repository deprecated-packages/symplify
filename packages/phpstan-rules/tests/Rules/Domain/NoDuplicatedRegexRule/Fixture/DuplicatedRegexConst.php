<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\NoDuplicatedRegexRule\Fixture;

final class DuplicatedRegexConst
{
    public const FIRST_REGEX = '#\d+#';

    public const SECOND_REGEX = '#\d+#';
}
