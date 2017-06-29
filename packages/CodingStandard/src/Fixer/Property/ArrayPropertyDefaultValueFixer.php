<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Property;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\DefinedFixerInterface;
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
            []
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

            $semicolonOrDefaultValuePosition = $tokens->getNextMeaningfulToken($index + 4);
            $variableToken = $tokens->getNextTokenOfKind($index, [T_VARIABLE]);
            // use getNextTokenOfKind to search for T_VARIABLE
            if ($variableToken === null) {
                break;
            }

            $semicolonOrDefaultValueToken = $tokens[$semicolonOrDefaultValuePosition];
            if (! $semicolonOrDefaultValueToken->equals(';')) {
                $this->addDefaultValueForArrayProperty($tokens, $semicolonOrDefaultValuePosition);
            }
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
        $docBlock = new DocBlock($token->getContent());

        if (count($docBlock->getLines()) === 1) {
            return false;
        }

        if (! $docBlock->getAnnotationsOfType('var')) {
            return false;
        }

        $varAnnotation = $docBlock->getAnnotationsOfType('var')[0];
        if (! Strings::contains($varAnnotation->getTypes()[0], '[]')) {
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
        ]);
    }
}
