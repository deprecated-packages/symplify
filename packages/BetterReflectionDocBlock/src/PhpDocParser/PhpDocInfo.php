<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;

final class PhpDocInfo
{
    /**
     * @var PhpDocNode
     */
    private $phpDocNode;

    /**
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @var PhpDocNode
     */
    private $originalPhpDocNode;

    /**
     * @param mixed[] $tokens
     */
    public function __construct(PhpDocNode $phpDocNode, array $tokens)
    {
        $this->phpDocNode = $phpDocNode;
        $this->tokens = $tokens;
        $this->originalPhpDocNode = clone $phpDocNode;
    }

    public function getPhpDocNode(): PhpDocNode
    {
        return $this->phpDocNode;
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
