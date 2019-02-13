<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocInfo;

use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwareParamTagValueNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwareReturnTagValueNode;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\AttributeAwareVarTagValueNode;
use Symplify\BetterPhpDocParser\Attributes\Attribute\Attribute;
use Symplify\BetterPhpDocParser\Attributes\Contract\Ast\AttributeAwareNodeInterface;
use Symplify\BetterPhpDocParser\PhpDocModifier;

final class PhpDocInfo
{
    /**
     * @var string
     */
    private $originalContent;

    /**
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @var PhpDocNode
     */
    private $phpDocNode;

    /**
     * @var PhpDocNode
     */
    private $originalPhpDocNode;

    /**
     * @var PhpDocModifier
     */
    private $phpDocModifier;

    /**
     * @param mixed[] $tokens
     */
    public function __construct(
        PhpDocNode $phpDocNode,
        array $tokens,
        string $originalContent,
        PhpDocModifier $phpDocModifier
    ) {
        $this->phpDocNode = $phpDocNode;
        $this->tokens = $tokens;
        $this->originalPhpDocNode = clone $phpDocNode;
        $this->originalContent = $originalContent;
        $this->phpDocModifier = $phpDocModifier;
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

    public function getParamTagValueByName(string $name): ?AttributeAwareParamTagValueNode
    {
        $phpDocNode = $this->getPhpDocNode();

        /** @var AttributeAwareParamTagValueNode $paramTagValue */
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

    public function getVarTagValue(): ?AttributeAwareVarTagValueNode
    {
        return $this->getPhpDocNode()->getVarTagValues()[0] ?? null;
    }

    public function getReturnTagValue(): ?AttributeAwareReturnTagValueNode
    {
        return $this->getPhpDocNode()->getReturnTagValues()[0] ?? null;
    }

    /**
     * @return AttributeAwareParamTagValueNode[]
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

    /**
     * @return AttributeAwareNodeInterface|TypeNode
     */
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

        return $paramTagValue->getAttribute(Attribute::TYPE_AS_ARRAY);
    }

    /**
     * @return string[]
     */
    public function getVarTypes(): array
    {
        return $this->getVarTagValue() ? $this->getVarTagValue()->getAttribute(Attribute::TYPE_AS_ARRAY) : [];
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

        return $returnTypeValueNode->getAttribute(Attribute::TYPE_AS_ARRAY);
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
