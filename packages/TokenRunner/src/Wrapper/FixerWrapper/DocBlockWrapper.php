<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use phpDocumentor\Reflection\DocBlock as PhpDocumentorDocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use Symplify\BetterReflectionDocBlock\CleanDocBlockFactory;
use Symplify\BetterReflectionDocBlock\DocBlockSerializerFactory;
use Symplify\BetterReflectionDocBlock\Tag\TolerantParam;
use Symplify\BetterReflectionDocBlock\Tag\TolerantReturn;
use Symplify\TokenRunner\DocBlock\ArrayResolver;
use Symplify\TokenRunner\Exception\Wrapper\FixerWrapper\MissingWhitespacesFixerConfigException;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

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
     * @var PhpDocumentorDocBlock
     */
    private $phpDocumentorDocBlock;

    /**
     * @var Serializer|null
     */
    private $docBlockSerializer;

    /**
     * @var string
     */
    private $originalContent;

    private function __construct(Tokens $tokens, int $position, string $content)
    {
        $this->tokens = $tokens;
        $this->position = $position;

        $this->phpDocumentorDocBlock = (new CleanDocBlockFactory())->create($content);
        $this->originalContent = $content;
    }

    public static function createFromTokensAndPosition(Tokens $tokens, int $index): self
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$index], [T_COMMENT, T_DOC_COMMENT], __METHOD__);

        return new self($tokens, $index, $tokens[$index]->getContent());
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

    public function getReturnType(): ?string
    {
        /** @var Return_[] $returnTags */
        $returnTags = $this->phpDocumentorDocBlock->getTagsByName('return');
        if (! $returnTags) {
            return null;
        }

        if ($returnTags[0]->getType() instanceof Array_) {
            return ArrayResolver::resolveArrayType($this->originalContent, $returnTags[0]->getType(), 'return');
        }

        if ($returnTags[0]->getType() instanceof Compound) {
            $types = [];
            foreach ($returnTags[0]->getType()->getIterator() as $singleTag) {
                if ($singleTag instanceof Array_) {
                    $types[] = ArrayResolver::resolveArrayType($this->originalContent, $singleTag, 'return');
                } else {
                    $types[] = ltrim((string) $singleTag, '\\');
                }
            }

            return implode('|', $types);
        }

        return $this->clean((string) $returnTags[0]);
    }

    public function getReturnTypeDescription(): ?string
    {
        /** @var Return_[] $returnTags */
        $returnTags = $this->phpDocumentorDocBlock->getTagsByName('return');
        if (! $returnTags) {
            return null;
        }

        return (string) $returnTags[0]->getDescription();
    }

    public function getArgumentType(string $name): ?string
    {
        $paramTag = $this->findParamTagByName($name);
        if ($paramTag) {
            // distinguish array vs mixed[]
            // false value resolve, @see https://github.com/phpDocumentor/TypeResolver/pull/48
            if ($paramTag->getType() instanceof Array_) {
                return ArrayResolver::resolveArrayType($this->originalContent, $paramTag->getType(), 'param', $name);
            }

            if ($paramTag->getType() instanceof Compound) {
                $types = [];
                foreach ($paramTag->getType()->getIterator() as $singleTag) {
                    if ($singleTag instanceof Array_) {
                        $types[] = ArrayResolver::resolveArrayType($this->originalContent, $singleTag, 'param', $name);
                    } else {
                        $types[] = (string) $singleTag;
                    }
                }

                return implode('|', $types);
            }

            return $this->clean((string) $paramTag->getType());
        }

        return null;
    }

    /**
     * @return Param[]
     */
    public function getParamTags(): array
    {
        return $this->phpDocumentorDocBlock->getTagsByName('param');
    }

    public function getVarTag(): ?Var_
    {
        return $this->phpDocumentorDocBlock->getTagsByName('var') ?
            $this->phpDocumentorDocBlock->getTagsByName('var')[0]
            : null;
    }

    public function getVarType(): ?string
    {
        $varTag = $this->getVarTag();
        if (! $varTag) {
            return null;
        }

        $varTagType = (string) $varTag->getType();
        $varTagType = trim($varTagType);

        return ltrim($varTagType, '\\');
    }

    public function getArgumentTypeDescription(string $name): ?string
    {
        $paramTag = $this->findParamTagByName($name);
        if ($paramTag) {
            return $this->clean((string) $paramTag->getDescription());
        }

        return null;
    }

    public function getReturnTag(): ?TolerantReturn
    {
        return $this->phpDocumentorDocBlock->getTagsByName('return') ?
            $this->phpDocumentorDocBlock->getTagsByName('return')[0]
            : null;
    }

    public function removeReturnType(): void
    {
        $returnTags = $this->phpDocumentorDocBlock->getTagsByName('return');
        if (! $returnTags) {
            return;
        }

        foreach ($returnTags as $returnTag) {
            $this->phpDocumentorDocBlock->removeTag($returnTag);
        }

        $this->updateDocBlockTokenContent();
    }

    public function removeParamType(string $name): void
    {
        $paramTag = $this->findParamTagByName($name);
        if (! $paramTag) {
            return;
        }

        $this->phpDocumentorDocBlock->removeTag($paramTag);

        $this->updateDocBlockTokenContent();
    }

    public function setWhitespacesFixerConfig(WhitespacesFixerConfig $whitespacesFixerConfig): void
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    public function isArrayProperty(): bool
    {
        $varTags = $this->phpDocumentorDocBlock->getTagsByName('var');
        if (! count($varTags)) {
            return false;
        }

        /** @var Var_ $varTag */
        $varTag = $varTags[0];

        $types = explode('|', trim((string) $varTag->getType()));

        foreach ($types as $type) {
            if (! self::isIterableType($type)) {
                return false;
            }
        }

        return true;
    }

    public function updateDocBlockTokenContent(): void
    {
        $this->tokens[$this->position] = new Token([T_DOC_COMMENT, $this->getContent()]);
    }

    public function getContent(): string
    {
        return $this->getDocBlockSerializer()
            ->getDocComment($this->phpDocumentorDocBlock);
    }

    private function isIterableType(string $type): bool
    {
        if (Strings::endsWith($type, '[]')) {
            return true;
        }

        if ($type === 'array') {
            return true;
        }

        return false;
    }

    private function clean(string $content): string
    {
        return ltrim(trim($content), '\\');
    }

    private function findParamTagByName(string $name): ?TolerantParam
    {
        $paramTags = $this->phpDocumentorDocBlock->getTagsByName('param');

        /** @var TolerantParam $paramTag */
        foreach ($paramTags as $paramTag) {
            if ($paramTag->getVariableName() === $name) {
                return $paramTag;
            }
        }

        return null;
    }

    private function getDocBlockSerializer(): Serializer
    {
        if ($this->docBlockSerializer) {
            return $this->docBlockSerializer;
        }

        $this->ensureWhitespacesFixerConfigIsSet();

        return $this->docBlockSerializer = DocBlockSerializerFactory::createFromWhitespaceFixerConfigAndContent(
            $this->whitespacesFixerConfig,
            $this->originalContent
        );
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
}
