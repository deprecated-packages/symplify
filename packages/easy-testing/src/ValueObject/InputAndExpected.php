<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject;

final class InputAndExpected
{
    /**
     * @param mixed $expected
     */
    public function __construct(
        private string $input,
        private $expected
    ) {
    }

    public function getInput(): string
    {
        return $this->input;
    }

    /**
     * @return mixed
     */
    public function getExpected()
    {
        return $this->expected;
    }
}
