<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Parser\TokenIterator;

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

    /**
     * @var TokenIterator
     */
    private $tokenIterator;

    /**
     * @var PhpDocNode
     */
    private $oldPhpDocNode;

    public function __construct(PhpDocNode $phpDocNode, bool $isSingleLineDoc, TokenIterator $tokenIterator)
    {
        $this->phpDocNode = $phpDocNode;
        $this->oldPhpDocNode = clone $phpDocNode;
        $this->tokenIterator = $tokenIterator;
        $this->isSingleLineDoc = $isSingleLineDoc;
    }

    public function getOldPhpDocNode(): PhpDocNode
    {
        return $this->oldPhpDocNode;
    }

    public function getPhpDocNode(): PhpDocNode
    {
        return $this->phpDocNode;
    }

    public function isSingleLineDoc(): bool
    {
        return $this->isSingleLineDoc;
    }

    public function getTokenIterator(): TokenIterator
    {
        return $this->tokenIterator;
    }
}
