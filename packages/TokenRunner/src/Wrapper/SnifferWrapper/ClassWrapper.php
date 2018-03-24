<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\SnifferWrapper;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\TokenHelper;
use Symplify\TokenRunner\Analyzer\SnifferAnalyzer\Naming;

final class ClassWrapper
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

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

    public function getClassName(): string
    {
        return $this->naming->getClassName($this->file, $this->position + 2);
    }

    public function implementsInterface(): bool
    {
        return (bool) $this->file->findNext(T_IMPLEMENTS, $this->position, $this->position + 15);
    }

    public function extends(): bool
    {
        return (bool) $this->file->findNext(T_EXTENDS, $this->position, $this->position + 5);
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
}
