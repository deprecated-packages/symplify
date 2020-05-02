<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Property;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\DocBlock\DocBlockManipulator;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\FixerClassWrapperFactory;

/**
 * @deprecated
 */
final class BoolPropertyDefaultValueFixer extends AbstractSymplifyFixer
{
    /**
     * @var FixerClassWrapperFactory
     */
    private $fixerClassWrapperFactory;

    /**
     * @var DocBlockManipulator
     */
    private $docBlockManipulator;

    public function __construct(
        FixerClassWrapperFactory $fixerClassWrapperFactory,
        DocBlockManipulator $docBlockManipulator
    ) {
        $this->fixerClassWrapperFactory = $fixerClassWrapperFactory;
        $this->docBlockManipulator = $docBlockManipulator;

        trigger_error(sprintf(
            'Fixer "%s" is deprecated. Use "%s" instead',
            self::class,
            'AddFalseDefaultToBoolPropertyRector'
        ));

        sleep(3);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Bool property should have default value, to prevent unintentional null comparison.',
            [new CodeSample('<?php
/**
 * @var bool
 */
public $isDisabled;')]
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
            $classWrapper = $this->fixerClassWrapperFactory->createFromTokensArrayStartPosition($tokens, $index);

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
            if (! $this->docBlockManipulator->isBoolProperty($tokens, $index)) {
                continue;
            }

            $equalTokenPosition = $tokens->getNextTokenOfKind($index, ['=']);
            $semicolonTokenPosition = (int) $tokens->getNextTokenOfKind($index, [';']);

            if ($this->isDefaultDefinitionSet($equalTokenPosition, $semicolonTokenPosition)) {
                continue;
            }

            $tokens->insertAt($semicolonTokenPosition, $this->createDefaultFalseTokens());
        }
    }

    private function isDefaultDefinitionSet(?int $equalTokenPosition, int $semicolonTokenPosition): bool
    {
        return is_numeric($equalTokenPosition) && $equalTokenPosition < $semicolonTokenPosition;
    }

    /**
     * " = false"
     */
    private function createDefaultFalseTokens(): Tokens
    {
        return Tokens::fromArray([
            new Token([T_WHITESPACE, ' ']),
            new Token('='),
            new Token([T_WHITESPACE, ' ']),
            new Token([T_STRING, 'false']),
        ]);
    }
}
