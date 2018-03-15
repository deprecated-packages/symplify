<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;

final class PhpDocInfo
{
    /**
     * @var PhpDocNode
     */
    private $phpDocNode;

    /**
     * @var bool
     */
    private $isSingleLineDoc;

    public function __construct(PhpDocNode $phpDocNode, bool $isSingleLineDoc)
    {
        $this->phpDocNode = $phpDocNode;
        $this->isSingleLineDoc = $isSingleLineDoc;
    }

    public function getPhpDocNode(): PhpDocNode
    {
        return $this->phpDocNode;
    }

    public function isSingleLineDoc(): bool
    {
        return $this->isSingleLineDoc;
    }

    public function __toString(): string
    {
        if ($this->isSingleLineDoc) {
            return sprintf('/** %s */', implode(' ', $this->phpDocNode->children));
        }

        return (string) $this->phpDocNode;
    }
}
