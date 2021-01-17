<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ValueObject;

use Nette\Utils\Strings;

final class Package
{
    /**
     * @var string
     */
    private $shortName;

    /**
     * @var bool
     */
    private $hasTests = false;

    public function __construct(string $name, bool $hasTests)
    {
        $this->shortName = (string) Strings::after($name, '/', -1);
        $this->hasTests = $hasTests;
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
