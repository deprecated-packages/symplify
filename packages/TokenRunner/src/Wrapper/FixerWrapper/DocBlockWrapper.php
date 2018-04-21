<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfo;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfoPrinter;
use Symplify\CodingStandard\Exception\NotImplementedYetException;
use Symplify\TokenRunner\Exception\Wrapper\FixerWrapper\MissingWhitespacesFixerConfigException;

final class DocBlockWrapper
{
    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var int
     */
    private $position;

    /**
     * @var WhitespacesFixerConfig|null
     */
    private $whitespacesFixerConfig;

    /**
     * @var string
     */
    private $originalContent;

    /**
     * @var null|PhpDocInfo
     */
    private $phpDocInfo;

    /**
     * @var PhpDocInfoPrinter
     */
    private $phpDocInfoPrinter;

    public function __construct(
        Tokens $tokens,
        int $position,
        string $content,
        ?PhpDocInfo $phpDocInfo = null,
        PhpDocInfoPrinter $phpDocInfoPrinter
    ) {
        $this->tokens = $tokens;
        $this->position = $position;
        $this->originalContent = $content;
        $this->phpDocInfo = $phpDocInfo;
        $this->phpDocInfoPrinter = $phpDocInfoPrinter;
    }

    public function getTokenPosition(): int
    {
        return $this->position;
    }

    public function isSingleLine(): bool
    {
        return substr_count($this->originalContent, PHP_EOL) < 1;
    }

    public function getMultiLineVersion(): string
    {
        $this->ensureWhitespacesFixerConfigIsSet();

        $newLineIndent = $this->whitespacesFixerConfig->getLineEnding() . $this->whitespacesFixerConfig->getIndent();

        return str_replace([' @', '/** ', ' */'], [
            $newLineIndent . ' * @',
            $newLineIndent . '/**',
            $newLineIndent . ' */',
        ], $this->originalContent);
    }

    public function getPhpDocInfo(): PhpDocInfo
    {
        return $this->phpDocInfo;
    }

    public function getArgumentType(string $name): ?string
    {
        $paramTagValue = $this->getPhpDocInfo()->getParamTagValueByName($name);
        if ($paramTagValue === null) {
            return '';
        }

        return $this->resolveDocType($paramTagValue->type);
    }

    public function getVarType(): ?string
    {
        $varTagValue = $this->phpDocInfo->getVarTagValue();
        if ($varTagValue === null) {
            return null;
        }

        return $this->resolveDocType($varTagValue->type);
    }

    public function getArgumentTypeDescription(string $name): string
    {
        $paramTagValue = $this->phpDocInfo->getParamTagValueByName($name);
        if ($paramTagValue) {
            return $paramTagValue->description;
        }

        return '';
    }

    public function removeReturnType(): void
    {
        $phpDocNode = $this->phpDocInfo->getPhpDocNode();

        foreach ($phpDocNode->children as $i => $phpDocChildNode) {
            if ($phpDocChildNode instanceof PhpDocTagNode && $phpDocChildNode->value instanceof ReturnTagValueNode) {
                unset($phpDocNode->children[$i]);
            }
        }
    }

    public function removePhpDocTagValueNode(PhpDocTagValueNode $phpDocTagValueNode): void
    {
        $phpDocNode = $this->phpDocInfo->getPhpDocNode();
        foreach ($phpDocNode->children as $key => $phpDocChildNode) {
            if (! $phpDocChildNode instanceof PhpDocTagNode) {
                continue;
            }

            if ($phpDocChildNode->value === $phpDocTagValueNode) {
                unset($phpDocNode->children[$key]);
            }
        }
    }

    public function removeParamType(string $name): void
    {
        $phpDocNode = $this->phpDocInfo->getPhpDocNode();
        $paramTagValue = $this->phpDocInfo->getParamTagValueByName($name);

        foreach ($phpDocNode->children as $key => $phpDocChildNode) {
            if (! property_exists($phpDocChildNode, 'value')) {
                continue;
            }

            // process invalid tag values
            if ($phpDocChildNode->value instanceof InvalidTagValueNode) {
                if ($phpDocChildNode->value->value === '$' . $name) {
                    unset($phpDocNode->children[$key]);
                    continue;
                }
            }

            if ($phpDocChildNode->value === $paramTagValue) {
                unset($phpDocNode->children[$key]);
            }
        }
    }

    public function setWhitespacesFixerConfig(WhitespacesFixerConfig $whitespacesFixerConfig): void
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    public function isArrayProperty(): bool
    {
        $varTagValue = $this->phpDocInfo->getVarTagValue();
        if ($varTagValue === null) {
            return false;
        }

        return $this->isIterableType($varTagValue->type);
    }

    public function getContent(): string
    {
        return $this->phpDocInfoPrinter->printFormatPreserving($this->phpDocInfo);
    }

    private function ensureWhitespacesFixerConfigIsSet(): void
    {
        if ($this->whitespacesFixerConfig) {
            return;
        }

        throw new MissingWhitespacesFixerConfigException(sprintf(
            '"%s% is not set to "%s". Use %s interface on your Fixer '
            . 'and pass it via `$docBlockWrapper->setWhitespacesFixerConfig()`',
            WhitespacesFixerConfig::class,
            self::class,
            WhitespacesAwareFixerInterface::class
        ));
    }

    public function saveNewPhpDocInfo(): void
    {
        $newDocCommentContent = $this->phpDocInfoPrinter->printFormatPreserving($this->phpDocInfo);
        if ($newDocCommentContent) {
            // create and save new doc comment
            $this->tokens[$this->position] = new Token([T_DOC_COMMENT, $newDocCommentContent]);
            return;
        }

        // remove empty doc
        $this->tokens->clearAt($this->position);
        if ($this->tokens[$this->position - 1]->isWhitespace()) {
            // used from RemoveEmptyDocBlockFixer
            $this->removeExtraWhitespaceAfterRemovedDocBlock();
        }
    }

    private function removeExtraWhitespaceAfterRemovedDocBlock(): void
    {
        $previousToken = $this->tokens[$this->position - 1];
        if ($previousToken->isWhitespace()) {
            $previousWhitespaceContent = $previousToken->getContent();

            $lastLineBreak = strrpos($previousWhitespaceContent, PHP_EOL);
            $newWhitespaceContent = substr($previousWhitespaceContent, 0, $lastLineBreak);
            if ($newWhitespaceContent) {
                $this->tokens[$this->position - 1] = new Token([T_WHITESPACE, $newWhitespaceContent]);
            } else {
                $this->tokens->clearAt($this->position - 1);
            }
        }
    }

    public function resolveDocType(TypeNode $typeNode): string
    {
        if ($typeNode instanceof ArrayTypeNode) {
            return $this->resolveDocType($typeNode->type) . '[]';
        }

        if ($typeNode instanceof IdentifierTypeNode || $typeNode instanceof ThisTypeNode) {
            return (string) $typeNode;
        }

        if ($typeNode instanceof UnionTypeNode) {
            $resolvedDocTypes = [];
            foreach ($typeNode->types as $subTypeNode) {
                $resolvedDocTypes[] = $this->resolveDocType($subTypeNode);
            }
            return implode('|', $resolvedDocTypes);
        }

        throw new NotImplementedYetException(sprintf(
            'Add new "%s" type format to "%s" method',
            get_class($typeNode),
            __METHOD__
        ));
    }

    private function isIterableType(TypeNode $typeNode): bool
    {
        if ($typeNode instanceof UnionTypeNode) {
            foreach ($typeNode->types as $subType) {
                if (! $this->isIterableType($subType)) {
                    return false;
                }
            }

            return true;
        }

        if ($typeNode instanceof IdentifierTypeNode) {
            if ($typeNode->name === 'array') {
                return true;
            }

            return false;
        }

        if ($typeNode instanceof ArrayTypeNode) {
            return true;
        }

        return false;
    }
}
