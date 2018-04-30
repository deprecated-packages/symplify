<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocParser;

use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Symplify\BetterPhpDocParser\PhpDocModifier;

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

    public function getVarTypeNode(): ?TypeNode
    {
        return $this->getVarTagValue() ? $this->getVarTagValue()->type : null;
    }

    // replace section

    public function replaceTagByAnother(string $oldTag, string $newTag)
    {
        $oldTag = '@' . ltrim($oldTag, '@');
        $newTag = '@' . ltrim($newTag, '@');

        foreach ($this->phpDocNode->children as $key => $phpDocChildNode) {
            if (! $phpDocChildNode instanceof PhpDocTagNode) {
                continue;
            }

            if ($phpDocChildNode->name === $oldTag) {
                $this->phpDocNode->children[$key] = new PhpDocTagNode($newTag, new GenericTagValueNode(''));
            }
        }
    }

    // remove section

    public function removeReturnTag(): void
    {
        foreach ($this->phpDocNode->getReturnTagValues() as $returnTagValue) {
            $this->phpDocModifier->removeTagFromPhpDocNode($this->phpDocNode, $returnTagValue);
        }
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
