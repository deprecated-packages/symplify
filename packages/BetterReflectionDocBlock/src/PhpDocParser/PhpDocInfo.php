<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
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
     * @var PhpDocNode
     */
    private $originalPhpDocNode;

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
        $this->originalPhpDocNode = clone $phpDocNode;
    }

    public function getPhpDocNode(): PhpDocNode
    {
        return $this->phpDocNode;
    }

    public function getTokenIterator(): TokenIterator
    {
        return $this->tokenIterator;
    }

    public function getOriginalPhpDocNode(): PhpDocNode
    {
        return $this->originalPhpDocNode;
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

    public function getParamTagValueByName(string $name): ?ParamTagValueNode
    {
        $phpDocNode = $this->getPhpDocNode();

        foreach ($phpDocNode->getParamTagValues() as $paramTagValue) {
            if (Strings::match($paramTagValue->parameterName, '#^(\$)?' . $name . '$#')) {
                return $paramTagValue;
            }
        }

        return null;
    }

    public function getVarTagValue(): ?VarTagValueNode
    {
        return $this->getPhpDocNode()->getVarTagValues()[0] ?? null;
    }

    public function getReturnTagValue(): ?ReturnTagValueNode
    {
        return $this->getPhpDocNode()->getReturnTagValues()[0] ?? null;
    }

    /**
     * @return ParamTagValueNode[]
     */
    public function getParamTagValues(): array
    {
        return $this->getPhpDocNode()->getParamTagValues();
    }
}
