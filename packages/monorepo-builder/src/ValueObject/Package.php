<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ValueObject;

use Nette\Utils\Strings;

final class Package
{
    private string $shortName;

    public function __construct(
        string $name,
        private bool $hasTests
    ) {
        $this->shortName = (string) Strings::after($name, '/', -1);
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function hasTests(): bool
    {
        return $this->hasTests;
    }
}
