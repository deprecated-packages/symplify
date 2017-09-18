<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Naming;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\FixerTokenWrapper\MethodWrapper;
use Symplify\CodingStandard\FixerTokenWrapper\PropertyWrapper;
use Symplify\CodingStandard\Tokenizer\ClassTokensAnalyzer;

final class PropertyNameMatchingTypeFixer extends AbstractFixer
{
    /**
     * @var string[]
     */
    private $changedPropertyNames = [];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Property name should match its type, if possible.', [
            new CodeSample(
                '<?php
class SomeClass
{
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }
}'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_STRING)
            && $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * @param SplFileInfo $file
     * @param Tokens $tokens
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (! $token->isClassy()) {
                continue;
            }

            $classTokenAnalyzer = ClassTokensAnalyzer::createFromTokensArrayStartPosition($tokens, $index);

            foreach ($classTokenAnalyzer->getProperties() as $propertyIndex => $propertyToken) {
                $this->fixProperty($tokens, $propertyIndex);
            }

            foreach ($classTokenAnalyzer->getMethods() as $methodIndex => $methodToken) {
                $this->fixMethod($tokens, $methodIndex);
            }

            // fix properties inside variables
        }
    }

    private function fixProperty(Tokens $tokens, int $index): void
    {
        $propertyWrapper = PropertyWrapper::createFromTokensAndPosition($tokens, $index);

        $oldName = $propertyWrapper->getName();
        $expectedName = lcfirst($propertyWrapper->getType());

        if ($oldName !== $expectedName) {
            $propertyWrapper->changeName($expectedName);

            $this->changedPropertyNames[$oldName] = $expectedName;
        }
    }

    private function fixMethod(Tokens $tokens, int $methodIndex): void
    {
        $methodWrapper = MethodWrapper::createFromTokensAndPosition($tokens, $methodIndex);

        $methodArguments = $methodWrapper->getArguments();

        foreach ($methodArguments as $argumentWrapper) {
            if (! $argumentWrapper->isClassType()) {
                continue;
            }

            $oldName = $argumentWrapper->getName();
            $expectedName = lcfirst($argumentWrapper->getType());

            if ($oldName !== $expectedName) {
                $argumentWrapper->changeName($expectedName);
            }
        }
    }
}
