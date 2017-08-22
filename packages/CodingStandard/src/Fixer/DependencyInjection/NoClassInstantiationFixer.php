<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\DependencyInjection;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\PositionDetector;
use Symplify\CodingStandard\Fixer\TokenBuilder;

final class NoClassInstantiationFixer implements DefinedFixerInterface
{
    /**
     * @todo configurable!
     * @var string[]
     */
    private $allowedClasses = [
        'DateTime'
    ];

    /**
     * @var int
     */
    private $classOpenerPosition;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Use service and constructor injection rather than manual new <class>.',
            [
                new CodeSample('<?php
class PostRepository
{
    public function getAll()
    {   
        $database = new Database();
        return $database->getPosts();
    }
}'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_NEW]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_CLASS)) {
                $possibleClassNamePosition = $tokens->getNextMeaningfulToken($index);
                if ($tokens[$possibleClassNamePosition]->isGivenKind(T_STRING)) {
                    // is probably class and its name
                    $this->classOpenerPosition = $tokens->getNextTokenOfKind($index, ['{']);
                }
            }

            if (! $token->isGivenKind(T_NEW)) {
                continue;
            }

            $classNamePosition = $tokens->getNextMeaningfulToken($index);
            $classNameToken = $tokens[$classNamePosition];

            if ($this->isClassInstantiationAllowed($classNameToken->getContent())) {
                continue;
            }

            $propertyNamePosition = $tokens->getPrevMeaningfulToken($index - 2); // to skip "="
            $propertyNameToken = $tokens[$propertyNamePosition];
            $propertyName = ltrim($propertyNameToken->getContent(), '$');

            $propertyType = $classNameToken->getContent();

            // 1. add property with type
            $propertyTokens = TokenBuilder::createPropertyTokens($propertyType, $propertyName);
            $tokens->insertAt($this->classOpenerPosition + 1, $propertyTokens);

            // 2. replace "new Database" => with "$this->database" (very dummy fix, but the most safe)
            $tokens[$index] = new Token([T_VARIABLE, '$this']);
            $tokens[$index + 1] = new Token([T_OBJECT_OPERATOR, '->']);
            $tokens[$classNamePosition] = new Token([T_STRING, $propertyName]);

            // 3. add dependency to constructor

            // A. has a constructor?
            $constructorPosition = PositionDetector::detectConstructorPosition($tokens);
            if ($constructorPosition) { // "function" token
                $this->addPropertyToConstructor($tokens, $propertyType, $propertyName, $constructorPosition);
            } else {
                // B. doesn't have a constructor
                $this->addConstructorMethod($tokens, $propertyType, $propertyName);
            }

//            $this->fix($file, $tokens);
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
        // run before class element sorting
        return 0;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function addConstructorMethod(Tokens $tokens, string $propertyType, string $propertyName): void
    {
        $constructorPosition = $this->getConstructorPosition($tokens);
        $constructorTokens = TokenBuilder::createConstructorWithPropertyTokens($propertyType, $propertyName);

        $tokens->insertAt($constructorPosition, $constructorTokens);
    }

    private function getConstructorPosition(Tokens $tokens): int
    {
        // 1. after last property
        for ($index = count($tokens) - 1; $index > 1; --$index) {
            $token = $tokens[$index];
            if ($token->isGivenKind(T_VARIABLE)) {
                $propertyEndSemicolonPosition = $tokens->getNextTokenOfKind($index, [';']);

                return $propertyEndSemicolonPosition + 1;
            }
        }

        // 2. before first method
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_FUNCTION)) {
                $methodStartPosition = $tokens->getPrevTokenOfKind(
                    $index,
                    [T_PUBLIC, T_PRIVATE, T_PROTECTED, T_DOC_COMMENT]
                );

                return $methodStartPosition - 1;
            }
        }
    }

    private function addPropertyToConstructor(
        Tokens $tokens,
        string $propertyType,
        string $propertyName,
        int $constructorPosition
    ): void {
        $startParenthesisIndex = $tokens->getNextTokenOfKind($constructorPosition, ['(', ';', T_CLOSE_TAG]);
        if (! $tokens[$startParenthesisIndex]->equals('(')) {
            return;
        }

        $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex);

        $tokens->insertAt($endParenthesisIndex, TokenBuilder::createLastArgumentTokens($propertyType, $propertyName));

        // detect end brace
        $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex);
        $startBraceIndex = $tokens->getNextTokenOfKind($endParenthesisIndex, [';', '{']);
        $endBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startBraceIndex);

        // add property as last assignment
        $tokens->insertAt($endBraceIndex - 1, TokenBuilder::createPropertyAssignmentTokens($propertyName));
    }

    private function isClassInstantiationAllowed(string $class): bool
    {
        return in_array($class, $this->allowedClasses, true);
    }
}
