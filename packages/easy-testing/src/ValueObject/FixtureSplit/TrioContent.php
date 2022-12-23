<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject\FixtureSplit;

/**
 * @api
 */
final class TrioContent
{
    public function __construct(
        private string $firstValue,
        private string $secondValue,
        private string $expectedResult
    ) {
    }

    public function getFirstValue(): string
    {
        return $this->firstValue;
    }

    public function getSecondValue(): string
    {
        return $this->secondValue;
    }

    public function getExpectedResult(): string
    {
        return $this->expectedResult;
    }
}
