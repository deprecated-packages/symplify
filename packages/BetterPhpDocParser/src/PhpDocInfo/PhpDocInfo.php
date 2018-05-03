<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocInfo;

use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterPhpDocParser\PhpDocModifier;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeToStringsConvertor;

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
     * @var string
     */
    private $originalContent;

    /**
     * @var PhpDocModifier
     */
    private $phpDocModifier;

    /**
     * @var TypeNodeToStringsConvertor
     */
    private $typeNodeToStringsConvertor;

    /**
     * @param mixed[] $tokens
     */
    public function __construct(
        PhpDocNode $phpDocNode,
        array $tokens,
        string $originalContent,
        PhpDocModifier $phpDocModifier,
        TypeNodeToStringsConvertor $typeNodeToStringsConvertor
    ) {
        $this->phpDocNode = $phpDocNode;
        $this->tokens = $tokens;
        $this->originalPhpDocNode = clone $phpDocNode;
        $this->originalContent = $originalContent;
        $this->phpDocModifier = $phpDocModifier;
        $this->typeNodeToStringsConvertor = $typeNodeToStringsConvertor;
    }

    public function getOriginalContent(): string
    {
        return $this->originalContent;
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

    public function getParamTagDescriptionByName(string $name): string
    {
        $paramTagValue = $this->getParamTagValueByName($name);
        if ($paramTagValue) {
            return $paramTagValue->description;
        }

        return '';
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

    public function hasTag(string $name): bool
    {
        return (bool) $this->getTagsByName($name);
    }

    /**
     * @return PhpDocTagNode[]
     */
    public function getTagsByName(string $name): array
    {
        $name = '@' . ltrim($name, '@');

        return $this->phpDocNode->getTagsByName($name);
    }

    public function getParamTypeNode(string $paramName): ?TypeNode
    {
        $paramName = '$' . ltrim($paramName, '$');

        foreach ($this->phpDocNode->getParamTagValues() as $paramTagsValue) {
            if ($paramTagsValue->parameterName === $paramName) {
                return $paramTagsValue->type;
            }
        }

        return null;
    }

    /**
     * @return IdentifierTypeNode|UnionTypeNode|ArrayTypeNode
     */
    public function getVarTypeNode(): ?TypeNode
    {
        return $this->getVarTagValue() ? $this->getVarTagValue()->type : null;
    }

    // types

    /**
     * @return string[]
     */
    public function getParamTypes(string $name): array
    {
        $paramTagValue = $this->getParamTagValueByName($name);
        if ($paramTagValue === null) {
            return [];
        }

        return $this->typeNodeToStringsConvertor->convert($paramTagValue->type);
    }

    /**
     * @return string[]
     */
    public function getVarTypes(): array
    {
        $varTypeNode = $this->getVarTypeNode();
        if ($varTypeNode === null) {
            return [];
        }

        return $this->typeNodeToStringsConvertor->convert($varTypeNode);
    }

    /**
     * @return string[]
     */
    public function getReturnTypes(): array
    {
        $returnTypeValueNode = $this->getReturnTagValue();
        if ($returnTypeValueNode === null) {
            return [];
        }

        return $this->typeNodeToStringsConvertor->convert($returnTypeValueNode->type);
    }

    // replace section

    public function replaceTagByAnother(string $oldTag, string $newTag): void
    {
        $this->phpDocModifier->replaceTagByAnother($this->phpDocNode, $oldTag, $newTag);
    }

    public function replacePhpDocTypeByAnother(string $oldType, string $newType): void
    {
        $this->phpDocModifier->replacePhpDocTypeByAnother($this->phpDocNode, $oldType, $newType);
    }

    // remove section

    public function removeReturnTag(): void
    {
        $this->phpDocModifier->removeReturnTagFromPhpDocNode($this->phpDocNode);
    }

    public function removeParamTagByParameter(string $name): void
    {
        $this->phpDocModifier->removeParamTagByParameter($this, $name);
    }

    public function removeTagByName(string $tagName): void
    {
        $this->phpDocModifier->removeTagByName($this, $tagName);
    }

    public function removeTagByNameAndContent(string $tagName, string $tagContent): void
    {
        $this->phpDocModifier->removeTagByNameAndContent($this, $tagName, $tagContent);
    }
}
