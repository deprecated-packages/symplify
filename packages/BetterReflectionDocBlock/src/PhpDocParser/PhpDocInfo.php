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
     * @var TokenIterator
     */
    private $tokenIterator;

    public function __construct(PhpDocNode $phpDocNode, TokenIterator $tokenIterator)
    {
        $this->phpDocNode = $phpDocNode;
        $this->tokenIterator = $tokenIterator;
    }

    public function getPhpDocNode(): PhpDocNode
    {
        return $this->phpDocNode;
    }

    public function getTokenIterator(): TokenIterator
    {
        return $this->tokenIterator;
    }
}
