<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Naming;

use Nette\Utils\Strings;

final class UseImport
{
    /**
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $shortName;

    public function __construct(string $fullName, string $shortName)
    {
        $this->fullName = $fullName;
        $this->shortName = $shortName;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function startsWith(string $name): bool
    {
        return Strings::startsWith($name, $this->shortName);
    }

    /**
     * @return string[]
     */
    public function getNameParts(): array
    {
        return explode('\\', $this->fullName);
    }
}
