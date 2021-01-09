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

    public function __construct(string $name)
    {
        $this->shortName = (string) Strings::after($name, '/', -1);
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }
}
