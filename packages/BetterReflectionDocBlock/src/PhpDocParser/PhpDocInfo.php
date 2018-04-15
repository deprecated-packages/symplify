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

    /**
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @var string
     */
    private $originalContent;

    /**
     * @param mixed[] $tokens
     */
    public function __construct(
        PhpDocNode $phpDocNode,
        TokenIterator $tokenIterator,
        array $tokens,
        string $originalContent
    ) {
        $this->phpDocNode = $phpDocNode;
        $this->tokenIterator = $tokenIterator;
        $this->tokens = $tokens;
        $this->originalContent = $originalContent;
    }

    public function getPhpDocNode(): PhpDocNode
    {
        return $this->phpDocNode;
    }

    public function getTokenIterator(): TokenIterator
    {
        return $this->tokenIterator;
    }

    /**
     * @return mixed[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    public function getOriginalContent(): string
    {
        return $this->originalContent;
    }
}
