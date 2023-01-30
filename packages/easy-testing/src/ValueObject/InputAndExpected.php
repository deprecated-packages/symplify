<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject;

final class InputAndExpected
{
    public function __construct(
        private readonly string $input,
        private readonly mixed $expected
    ) {
    }

    public function getInput(): string
    {
        return $this->input;
    }

    public function getExpected(): mixed
    {
        return $this->expected;
    }
}
