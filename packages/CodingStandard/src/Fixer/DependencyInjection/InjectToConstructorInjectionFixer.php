<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\DependencyInjection;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\PositionDetector;
use Symplify\CodingStandard\Fixer\TokenBuilder;
use Symplify\CodingStandard\FixerTokenWrapper\PropertyWrapper;
use Symplify\CodingStandard\Tokenizer\ClassTokensAnalyzer;

final class InjectToConstructorInjectionFixer implements DefinedFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Constructor injection should be used instead of @inject annotations.',
            [
                new CodeSample('<?php
class SomeClass
{
    /**
     * @inject
     * @var stdClass
     */
    public $property;
}'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_DOC_COMMENT, T_PUBLIC, T_VARIABLE]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(T_CLASS)) {
                continue;
            }

            $classTokenAnalyzer = ClassTokensAnalyzer::createFromTokensArrayStartPosition($tokens, $index);
            $this->fixClass($tokens, $classTokenAnalyzer);
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

    private function fixClass(Tokens $tokens, ClassTokensAnalyzer $classTokenAnalyzer): void
    {
        $properties = $classTokenAnalyzer->getProperties();

        foreach ($properties as $index => ['token' => $propertyToken]) {
            $this->fixProperty($tokens, $index);
        }
    }

    private function fixProperty(Tokens $tokens, int $index): void
    {
        $propertyWrapper = PropertyWrapper::createFromTokensAndPosition($tokens, $index);

        if (! $propertyWrapper->isInjectProperty()) {
            return;
        }

        $propertyWrapper->removeAnnotation('inject');
        $propertyWrapper->makePrivate();

        $propertyName = $propertyWrapper->getName();
        $propertyType = $propertyWrapper->getType();

        // A. has a constructor?
        $constructorPosition = PositionDetector::detectConstructorPosition($tokens);
        if ($constructorPosition) { // "function" token
            $this->addPropertyToConstructor($tokens, $propertyType, $propertyName, $constructorPosition);
        } else {
            // B. doesn't have a constructor
            $this->addConstructorMethod($tokens, $propertyType, $propertyName);
        }
    }
}
