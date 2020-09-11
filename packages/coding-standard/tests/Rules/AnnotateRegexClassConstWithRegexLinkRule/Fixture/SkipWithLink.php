<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\AnnotateRegexClassConstWithRegexLinkRule\Fixture;

final class SkipWithLink
{
    /**
     * @var string
     * @see https://regex101.com/r/IMIpoN/1/
     */
    public const NAME_REGEX = '#super_long_one#';
}
