<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Property;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\DocBlockFinder;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapperFactory;
use Symplify\TokenRunner\Wrapper\FixerWrapper\DocBlockWrapperFactory;

final class ArrayPropertyDefaultValueFixer extends AbstractSymplifyFixer
{
    /**
     * @var Tokens|null
     */
    private static $cachedDefaultArrayTokens;

    /**
     * @var ClassWrapperFactory
     */
    private $classWrapperFactory;

    /**
     * @var DocBlockWrapperFactory
     */
    private $docBlockWrapperFactory;

    /**
     * @var DocBlockFinder
     */
    private $docBlockFinder;

    public function __construct(
        ClassWrapperFactory $classWrapperFactory,
        DocBlockWrapperFactory $docBlockWrapperFactory,
        DocBlockFinder $docBlockFinder
    ) {
        $this->classWrapperFactory = $classWrapperFactory;
        $this->docBlockWrapperFactory = $docBlockWrapperFactory;
        $this->docBlockFinder = $docBlockFinder;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Array property should have default value, to prevent undefined array issues.',
            [new CodeSample('<?php
/**
 * @var string[]
 */
public $property;')]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_CLASS, T_TRAIT])
            && $tokens->isAllTokenKindsFound([T_DOC_COMMENT, T_VARIABLE])
            && $tokens->isAnyTokenKindsFound([T_PUBLIC, T_PROTECTED, T_PRIVATE]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->getReversedClassyPositions($tokens) as $index) {
            $classWrapper = $this->classWrapperFactory->createFromTokensArrayStartPosition($tokens, $index);

            $this->fixProperties($tokens, $classWrapper->getProperties());
        }
    }

    /**
     * @param mixed[]|Token[] $properties
     */
    private function fixProperties(Tokens $tokens, array $properties): void
    {
        $properties = array_reverse($properties, true);

        foreach (array_keys($properties) as $index) {
            $docBlockTokenPosition = $this->docBlockFinder->findPreviousPosition($tokens, $index);
            if ($docBlockTokenPosition === null) {
                continue;
            }

            $docBlockWrapper = $this->docBlockWrapperFactory->create(
                $tokens,
                $docBlockTokenPosition,
                $tokens[$docBlockTokenPosition]->getContent()
            );

            if (! $docBlockWrapper->isArrayProperty()) {
                continue;
            }

            $equalTokenPosition = $tokens->getNextTokenOfKind($index, ['=']);
            $semicolonTokenPosition = (int) $tokens->getNextTokenOfKind($index, [';']);

            if ($this->isDefaultDefinitionSet($equalTokenPosition, $semicolonTokenPosition)) {
                continue;
            }

            $tokens->insertAt($semicolonTokenPosition, $this->createDefaultArrayTokens());
        }
    }

    private function isDefaultDefinitionSet(?int $equalTokenPosition, int $semicolonTokenPosition): bool
    {
        return is_numeric($equalTokenPosition) && $equalTokenPosition < $semicolonTokenPosition;
    }

    private function createDefaultArrayTokens(): Tokens
    {
        if (self::$cachedDefaultArrayTokens) {
            return self::$cachedDefaultArrayTokens;
        }

        $tokens = Tokens::fromArray([
            new Token([T_WHITESPACE, ' ']),
            new Token('='),
            new Token([T_WHITESPACE, ' ']),
            new Token([CT::T_ARRAY_SQUARE_BRACE_OPEN, '[']),
            new Token([CT::T_ARRAY_SQUARE_BRACE_CLOSE, ']']),
        ]);

        self::$cachedDefaultArrayTokens = $tokens;

        return $tokens;
    }
}
