<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\SnifferWrapper;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\TokenHelper;
use Symplify\CodingStandard\TokenRunner\Analyzer\SnifferAnalyzer\Naming;

final class SniffClassWrapper
{
    /**
     * @var int
     */
    private $position;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Naming
     */
    private $naming;

    public function __construct(File $file, int $position, Naming $naming)
    {
        $this->file = $file;
        $this->position = $position;
        $this->naming = $naming;
    }

    public function getClassName(): ?string
    {
        return $this->naming->getClassName($this->file, $this->position + 2);
    }

    /**
     * @return string[]
     */
    public function getPartialInterfaceNames(): array
    {
        if (! $this->doesImplementInterface()) {
            return [];
        }

        $implementPosition = $this->getImplementsPosition();
        $openBracketPosition = $this->file->findNext(T_OPEN_CURLY_BRACKET, $this->position, $this->position + 15);

        // anonymous class
        if (! $openBracketPosition) {
            return [];
        }

        $interfacePartialNamePosition = $this->file->findNext(T_STRING, $implementPosition, $openBracketPosition);

        $partialInterfacesNames = [];
        $partialInterfacesNames[] = $this->file->getTokens()[$interfacePartialNamePosition]['content'];

        return $partialInterfacesNames;
    }

    public function doesImplementInterface(): bool
    {
        return (bool) $this->getImplementsPosition();
    }

    public function getParentClassName(): ?string
    {
        $extendsTokenPosition = TokenHelper::findNext($this->file, T_EXTENDS, $this->position, $this->position + 10);
        if ($extendsTokenPosition === null) {
            return null;
        }

        $parentClassPosition = (int) TokenHelper::findNext($this->file, T_STRING, $extendsTokenPosition);

        return $this->naming->getClassName($this->file, $parentClassPosition);
    }

    /**
     * @return bool|int
     */
    private function getImplementsPosition()
    {
        return $this->file->findNext(T_IMPLEMENTS, $this->position, $this->position + 15);
    }
}
