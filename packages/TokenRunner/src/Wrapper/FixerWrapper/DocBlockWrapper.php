<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use phpDocumentor\Reflection\DocBlock as PhpDocumentorDocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Mixed_;
use Symplify\TokenRunner\DocBlock\DocBlockSerializerFactory;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class DocBlockWrapper
{
    /**
     * @var Tokens|null
     */
    private $tokens;

    /**
     * @var DocBlock|null
     */
    private $docBlock;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    /**
     * @var int|null
     */
    private $docBlockPosition;

    /**
     * @var PhpDocumentorDocBlock
     */
    private $phpDocumentorDocBlock;

    /**
     * @var Serializer
     */
    private $docBlockSerializer;

    /**
     * @var string
     */
    private $originalContent;

    private function __construct(?Tokens $tokens, ?int $docBlockPosition, ?DocBlock $docBlock, ?Token $token = null)
    {
        $this->tokens = $tokens;
        $this->docBlockPosition = $docBlockPosition;
        $this->docBlock = $docBlock;

        if ($docBlock === null && $token !== null) {
            $this->docBlock = new DocBlock($token->getContent());
        }

        $docBlockFactory = DocBlockFactory::createInstance();
        $content = $token ? $token->getContent() : $docBlock->getContent();
        $this->phpDocumentorDocBlock = $docBlockFactory->create($content);
        $this->originalContent = $content;
    }

    public static function createFromTokensPositionAndDocBlock(
        Tokens $tokens,
        int $docBlockPosition,
        DocBlock $docBlock
    ): self {
        TokenTypeGuard::ensureIsTokenType($tokens[$docBlockPosition], [T_COMMENT, T_DOC_COMMENT], __METHOD__);

        return new self($tokens, $docBlockPosition, $docBlock);
    }

    public static function createFromDocBlockToken(Token $docBlockToken): self
    {
        TokenTypeGuard::ensureIsTokenType($docBlockToken, [T_COMMENT, T_DOC_COMMENT], __METHOD__);

        return new self(null, null, null, $docBlockToken);
    }

    public function isSingleLine(): bool
    {
        return count($this->docBlock->getLines()) === 1;
    }

    public function changeToMultiLine(): void
    {
        $indent = $this->whitespacesFixerConfig->getIndent();
        $lineEnding = $this->whitespacesFixerConfig->getLineEnding();
        $newLineWithIndent = $lineEnding . $indent;

        $newDocBlock = str_replace(
            [' @', '/** ', ' */'],
            [
                $newLineWithIndent . ' * @',
                '/**',
                $newLineWithIndent . ' */',
            ],
            $this->docBlock->getContent()
        );

        $this->tokens[$this->docBlockPosition] = new Token([T_DOC_COMMENT, $newDocBlock]);
    }

    public function getReturnType(): ?string
    {
        $returnTags = $this->phpDocumentorDocBlock->getTagsByName('return');
        if (! $returnTags) {
            return null;
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
                if ($paramTag->getType()->getValueType() instanceof Mixed_) {
                    if ((bool) Strings::match($this->originalContent, sprintf('#array\s+\$%s#', $name))) {
                        return 'array';
                    }
                    if ((bool) Strings::match($this->originalContent, sprintf('#mixed\[\]\s+\$%s#', $name))) {
                        return 'mixed[]';
                    }
                }
            }

            return $this->clean((string) $paramTag->getType());
        }

        return null;
    }

    public function getArgumentTypeDescription(string $name): ?string
    {
        $paramTag = $this->findParamTagByName($name);
        if ($paramTag) {
            return $this->clean((string) $paramTag->getDescription());
        }

        return null;
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
        if (! $this->docBlock->getAnnotationsOfType('var')) {
            return false;
        }

        $varAnnotation = $this->docBlock->getAnnotationsOfType('var')[0];

        $content = trim($varAnnotation->getContent());
        $content = rtrim($content, ' */');

        [, $types] = explode('@var', $content);

        $types = explode('|', trim($types));

        foreach ($types as $type) {
            if (! self::isIterableType($type)) {
                return false;
            }
        }

        return true;
    }

    public function contains(string $content): bool
    {
        return Strings::contains($this->docBlock->getContent(), $content);
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

    private function findParamTagByName(string $name): ?Param
    {
        $paramTags = $this->phpDocumentorDocBlock->getTagsByName('param');

        /** @var Param $paramTag */
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

        return $this->docBlockSerializer = DocBlockSerializerFactory::createFromWhitespaceFixerConfig(
            $this->whitespacesFixerConfig
        );
    }

    private function updateDocBlockTokenContent(): void
    {
        $docBlockContent = $this->getDocBlockSerializer()
            ->getDocComment($this->phpDocumentorDocBlock);

        $this->tokens[$this->docBlockPosition] = new Token([T_DOC_COMMENT, $docBlockContent]);
    }
}
