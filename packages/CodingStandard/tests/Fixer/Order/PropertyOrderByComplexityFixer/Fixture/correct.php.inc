<?php

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Fixture;

final class SomeClassWithNullables
{
    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var \DateTime|null
     */
    private $startBracketIndex;
}
