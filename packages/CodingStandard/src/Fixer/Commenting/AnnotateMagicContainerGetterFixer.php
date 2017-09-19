<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Tokenizer\DocBlockFinder;

final class AnnotateMagicContainerGetterFixer implements FixerInterface, DefinedFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Variables created with $container->get(SomeService::class) should have annotation, '
            . 'so every IDE supports autocomplete without any plugins.',
            [
                new CodeSample('<?php
$variable = $container->get(SomeType::class);
'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_VARIABLE]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = count($tokens) - 1; $index > 1; --$index) {
            $token = $tokens[$index];

            if (! $token->isGivenKind(T_VARIABLE)) {
                continue;
            }

            $className = $this->getClassNameIfContainerCreatedVariable($tokens, $token, $index);
            if ($className === null) {
                continue;
            }

            $variableName = $token->getContent();

            // has variable a @var annotation?
            $docBlockToken = DocBlockFinder::findPrevious($tokens, $index);
            $docBlock = null;
            if ($docBlockToken instanceof Token) {
                $docBlock = new DocBlock($docBlockToken->getContent());
                $varAnnotations = $docBlock->getAnnotationsOfType('var');
                if (count($varAnnotations)) {
                    continue;
                }
            }

            // add doc block token before this one
            $previousWhitespacePosition = $tokens->getTokenNotOfKindSibling($index, -1, [T_WHITESPACE]);
            $whitespaceToken = clone $tokens[$previousWhitespacePosition];

            $tokens->insertAt($index, [
                $this->createDocCommentToken($className, $variableName),
                $whitespaceToken, // original space whitespace
            ]);
        }
    }

    public function isRisky(): bool
    {
        return false;
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

    private function getClassNameIfContainerCreatedVariable(Tokens $tokens, Token $token, int $position): ?string
    {
        if ($token->getContent() === '$this') {
            return null;
        }

        if ($this->isContainerGetCall($tokens, $position) === false) {
            return null;
        }

        $seekSequence = [
            new Token([T_DOUBLE_COLON, '::']),
            new Token([CT::T_CLASS_CONSTANT, 'class']),
        ];

        /** @var Token[] $nextVariableTokens */
        $nextVariableTokens = $tokens->findGivenKind(T_VARIABLE, $position + 1, $position + 5);
        $nextVariablePosition = key($nextVariableTokens);

        $foundSequence = $tokens->findSequence($seekSequence, $nextVariablePosition, $nextVariablePosition + 10);
        if ($foundSequence === null || count($foundSequence) === 0) {
            return null;
        }

        $classNameEndPosition = key($foundSequence) - 1;
        $className = $this->getContentUntilBracket($tokens, $classNameEndPosition);

        if ($className === '') {
            return null;
        }

        return $className;
    }

    private function getContentUntilBracket(Tokens $tokens, int $classNameEndPosition): string
    {
        $content = '';

        for ($i = $classNameEndPosition; $i > 0; --$i) {
            $token = $tokens[$i];

            if ($token->getContent() === '(') {
                return $content;
            }

            $content = $token->getContent() . $content;
        }

        return $content;
    }

    private function createDocCommentToken(string $className, string $variableName): Token
    {
        return new Token([T_DOC_COMMENT, sprintf(
            '/** @var %s %s */',
            $className,
            $variableName
        )]);
    }

    private function isContainerGetCall(Tokens $tokens, int $position): bool
    {
        $nextVariableTokens = $tokens->findGivenKind(T_VARIABLE, $position + 1, $position + 5);

        /** @var Token $nextVariableToken */
        $nextVariablePosition = key($nextVariableTokens);
        $nextVariableToken = array_pop($nextVariableTokens);

        if ($nextVariableToken->getContent() !== '$this') {
            return false;
        }

        /** @var Token[] $nextStringTokens */
        $nextStringTokens = $tokens->findGivenKind(T_STRING, $nextVariablePosition + 1, $nextVariablePosition + 5);

        // @todo: try token sequence

        // $this->container->get()
        if (count($nextStringTokens) === 2) {
            $firstToken = array_shift($nextStringTokens);
            $secondToken = array_shift($nextStringTokens);

            if (Strings::contains($firstToken->getContent(), 'container') && $secondToken->getContent() === 'get') {
                return true;
            }
        }

        // @todo: try token sequence

        // $this->get()
        if (count($nextVariableTokens) === 1) {
            $firstToken = array_pop($nextStringTokens);
            if ($firstToken->getContent() === 'get') {
                return true;
            }
        }

        return false;
    }
}
