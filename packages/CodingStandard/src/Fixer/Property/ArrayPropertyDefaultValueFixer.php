<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Property;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class ArrayPropertyDefaultValueFixer implements DefinedFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Array property should have default value, to prevent undefined array issues.',
            [
                new CodeSample(
'<?php
/**
 * @var string[]
 */
public $property;'
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        // analyze only properties with comments
        return $tokens->isAllTokenKindsFound([T_DOC_COMMENT, T_VARIABLE]);
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = count($tokens) - 1; $index > 1; --$index) {
            $token = $tokens[$index];

            if (! $token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            if (! $this->isArrayPropertyDocComment($token)) {
                continue;
            }

            // @todo: how to get variable here?
            $equalTokenPosition = $tokens->getNextTokenOfKind($index, ['=']);
            $semicolonTokenPosition = $tokens->getNextTokenOfKind($index, [';']);

            // default definition is set
            if (is_numeric($equalTokenPosition) && $equalTokenPosition < $semicolonTokenPosition) {
                continue;
            }

            $this->addDefaultValueForArrayProperty($tokens, $semicolonTokenPosition);
        }
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function isArrayPropertyDocComment(Token $token): bool
    {
        if (! $token->isComment()) {
            return false;
        }

        $docBlock = new DocBlock($token->getContent());

        if (count($docBlock->getLines()) === 1) {
            return false;
        }

        if (! $docBlock->getAnnotationsOfType('var')) {
            return false;
        }

        $varAnnotation = $docBlock->getAnnotationsOfType('var')[0];
        $varTypes = $varAnnotation->getTypes();
        if (! count($varTypes)) {
            return false;
        }

        if (! Strings::contains($varTypes[0], '[]')) {
            return false;
        }

        return true;
    }

    private function addDefaultValueForArrayProperty(Tokens $tokens, int $semicolonPosition): void
    {
        $tokens[$semicolonPosition]->clear();

        $tokens->insertAt($semicolonPosition, [
            new Token([T_WHITESPACE, ' ']),
            new Token('='),
            new Token([T_WHITESPACE, ' ']),
            new Token([CT::T_ARRAY_SQUARE_BRACE_OPEN, '[']),
            new Token([CT::T_ARRAY_SQUARE_BRACE_CLOSE, ']']),
            new Token(';'),
        ]);
    }
}
