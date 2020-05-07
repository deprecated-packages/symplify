<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\MatchingTypeConstantRule\Source;

final class ClassWithConstants
{
    /**
     * @var bool
     */
    public const IS_TRUE = 'really';

    /**
     * @var string
     */
    public const STRINGIFY = false;

    /**
     * @var bool
     */
    public const PATH = __DIR__;

    /**
     * @var float
     */
    public const FLOAT_ABOVE = 5.0;

    /**
     * @var string
     */
    public const NAME = 'Leo Babauta';

    public const EMPTY = 'Leo Babauta';

    /**
     * @var string
     */
    public const SKIP_CONCAT = 'Leo ' . ' Babauta';
}
