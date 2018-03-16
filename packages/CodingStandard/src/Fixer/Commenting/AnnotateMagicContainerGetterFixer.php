<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\DocBlockFinder;
use Symplify\TokenRunner\Wrapper\FixerWrapper\DocBlockWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\DocBlockWrapperFactory;

final class AnnotateMagicContainerGetterFixer implements DefinedFixerInterface
{
    /**
     * @var DocBlockWrapperFactory
     */
    private $docBlockWrapperFactory;

    public function __construct(DocBlockWrapperFactory $docBlockWrapperFactory)
    {
        $this->docBlockWrapperFactory = $docBlockWrapperFactory;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Variables created with $container->get(SomeService::class) should have annotation, '
            . 'so every IDE supports autocomplete without any plugins.',
            [
                new CodeSample('<?php
$someService = $container->get(SomeType::class);
$someService->unknownMethod();
'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_VARIABLE, T_OBJECT_OPERATOR, T_STRING]);
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

            // has variable a @var annotation?
            $docBlockPosition = DocBlockFinder::findPreviousPosition($tokens, $index);
            if ($docBlockPosition) {
                $docBlockWrapper = $this->docBlockWrapperFactory->create(
                    $tokens,
                    $docBlockPosition,
                    $tokens[$docBlockPosition]->getContent()
                );

                if ($docBlockWrapper->getVarType()) {
                    continue;
                }
            }

            // add doc block token before this one
            $previousWhitespacePosition = $tokens->getTokenNotOfKindSibling($index, -1, [T_WHITESPACE]);
            $whitespaceToken = clone $tokens[$previousWhitespacePosition];

            $whitespaceToken = $this->removeMultiWhitespaces($whitespaceToken);

            $variableName = $token->getContent();
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
        // is in tests
        if (Strings::contains($file->getRealPath(), 'tests' . DIRECTORY_SEPARATOR)) {
            return true;
        }

        // is bin file
        if (Strings::contains($file->getRealPath(), 'bin' . DIRECTORY_SEPARATOR)) {
            return true;
        }

        return false;
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
        return new Token([T_DOC_COMMENT, sprintf('/** @var %s %s */', $className, $variableName)]);
    }

    private function isContainerGetCall(Tokens $tokens, int $position): bool
    {
        /** @var Token[] $nextVariableTokens */
        $nextVariableTokens = $tokens->findGivenKind(T_VARIABLE, $position + 1, $position + 5);
        $nextVariablePosition = key($nextVariableTokens);

        if ($nextVariablePosition === null) {
            return false;
        }

        if ($tokens[$nextVariablePosition]->getContent() !== '$this') {
            return false;
        }

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

        return $thisContainerGetSequence !== null;
    }

    private function removeMultiWhitespaces(Token $whitespaceToken): Token
    {
        $newContent = str_replace([PHP_EOL . PHP_EOL, '\n' . '\n'], [PHP_EOL, '\n'], $whitespaceToken->getContent());

        return new Token([T_WHITESPACE, $newContent]);
    }
}
