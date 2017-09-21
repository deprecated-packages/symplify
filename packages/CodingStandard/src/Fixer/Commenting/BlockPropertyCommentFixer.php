<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\FixerTokenWrapper\PropertyWrapper;
use Symplify\CodingStandard\Tokenizer\ClassTokensAnalyzer;
use Symplify\CodingStandard\Tokenizer\DocBlockFinder;

final class BlockPropertyCommentFixer implements FixerInterface, DefinedFixerInterface, WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Block comment should be used instead of one liner.',
            [
                new CodeSample('<?php
/** @var SomeType */
private $property;
'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_CLASS, T_TRAIT]) &&
            $tokens->isAllTokenKindsFound([T_VARIABLE, T_DOC_COMMENT]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = count($tokens) - 1; $index > 1; --$index) {
            $token = $tokens[$index];

            if (! $token->isGivenKind(T_CLASS)) {
                continue;
            }

            $classTokenAnalyzer = ClassTokensAnalyzer::createFromTokensArrayStartPosition($tokens, $index);
            foreach ($classTokenAnalyzer->getProperties() as $propertyPosition => $propertyToken) {

                $propertyWrapper = PropertyWrapper::createFromTokensAndPosition($tokens, $propertyPosition);
                dump($propertyWrapper);
                die;
            }

            dump('beleee');
            die;

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

            $whitespaceToken = $this->removeMultiWhitespaces($whitespaceToken);

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

        /** @var Token[] $nextVariableTokens */
        $nextVariableTokens = $tokens->findGivenKind(T_VARIABLE, $position + 1, $position + 5);
        $nextVariablePosition = key($nextVariableTokens);

        $foundSequence = $tokens->findSequence([
            new Token([T_DOUBLE_COLON, '::']),
            new Token([CT::T_CLASS_CONSTANT, 'class']),
        ], $nextVariablePosition, $nextVariablePosition + 10);

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
        /** @var Token[] $nextVariableTokens */
        $nextVariableTokens = $tokens->findGivenKind(T_VARIABLE, $position + 1, $position + 5);
        $nextVariablePosition = key($nextVariableTokens);

        $thisGetSequence = $tokens->findSequence([
            new Token([T_VARIABLE, '$this']),
            new Token([T_OBJECT_OPERATOR, '->']),
            new Token([T_STRING, 'get']),
        ], $nextVariablePosition, $nextVariablePosition + 5);

        if ($thisGetSequence !== null) {
            return true;
        }

        $thisContainerGetSequence = $tokens->findSequence([
            new Token([T_VARIABLE, '$this']),
            new Token([T_OBJECT_OPERATOR, '->']),
            new Token([T_STRING, 'container']),
            new Token([T_OBJECT_OPERATOR, '->']),
            new Token([T_STRING, 'get']),
        ], $nextVariablePosition, $nextVariablePosition + 5);

        if ($thisContainerGetSequence !== null) {
            return true;
        }

        return false;
    }

    private function removeMultiWhitespaces(Token $whitespaceToken): Token
    {
        $newContent = str_replace([PHP_EOL . PHP_EOL, '\n' . '\n'], [PHP_EOL, '\n'], $whitespaceToken->getContent());

        return new Token([T_WHITESPACE, $newContent]);
    }

    public function setWhitespacesConfig(WhitespacesFixerConfig $config)
    {
        // TODO: Implement setWhitespacesConfig() method.
    }
}
