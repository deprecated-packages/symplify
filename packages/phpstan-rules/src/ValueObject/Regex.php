<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class Regex
{
    /**
     * someName\\Tests Tests\\someName someName\\Tests\\someOtherName
     *
     * @see https://regex101.com/r/6pPP8u/2
     * @var string
     */
    public const TESTS_PART_REGEX = '#(^Tests\\\\|\\\\Tests\\\\|\\\\Tests$)#';
}
