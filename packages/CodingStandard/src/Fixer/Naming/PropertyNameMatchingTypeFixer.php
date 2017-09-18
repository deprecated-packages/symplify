<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Naming;

use Nette\Utils\Strings;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\FixerTokenWrapper\ArgumentWrapper;
use Symplify\CodingStandard\FixerTokenWrapper\MethodWrapper;
use Symplify\CodingStandard\FixerTokenWrapper\PropertyWrapper;
use Symplify\CodingStandard\Tokenizer\ClassTokensAnalyzer;

final class PropertyNameMatchingTypeFixer extends AbstractFixer
{
    /**
     * @var ClassTokensAnalyzer|null
     */
    private $classTokenAnalyzer;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Property name should match its type, if possible.', [
            new CodeSample(
                '<?php
class SomeClass
{
    public function __construct(EntityManagerInterface $manager)
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
                $this->classTokenAnalyzer = null;
                continue;
            }

            $this->classTokenAnalyzer = ClassTokensAnalyzer::createFromTokensArrayStartPosition($tokens, $index);

            $this->fixClassProperties($tokens);

            foreach ($this->classTokenAnalyzer->getMethods() as $methodIndex => $methodToken) {
                $this->fixMethod($tokens, $methodIndex);
            }
        }
    }

    private function fixClassProperties(Tokens $tokens): void
    {
        $changedPropertyNames = [];

        foreach ($this->classTokenAnalyzer->getProperties() as $propertyIndex => $propertyToken) {
            $propertyWrapper = PropertyWrapper::createFromTokensAndPosition($tokens, $propertyIndex);

            if ($propertyWrapper->getType() === null || ! $propertyWrapper->isClassType()) {
                continue;
            }

            $expectedName = $this->getExpectedNameFromType($propertyWrapper->getType());

            $oldName = $propertyWrapper->getName();
            if ($oldName === $expectedName) {
                continue;
            }

            $propertyWrapper->changeName($expectedName);

            $changedPropertyNames[$oldName] = $expectedName;
        }

        foreach ($changedPropertyNames as $oldName => $newName) {
            $this->classTokenAnalyzer->renameEveryPropertyOccurrence($oldName, $newName);
        }
    }

    private function fixMethod(Tokens $tokens, int $methodIndex): void
    {
        $methodWrapper = MethodWrapper::createFromTokensAndPosition($tokens, $methodIndex);
        $changedVariableNames = [];

        /** @var ArgumentWrapper[] $arguments */
        $arguments = array_reverse($methodWrapper->getArguments());

        foreach ($arguments as $argumentWrapper) {
            if (! $argumentWrapper->isClassType()) {
                continue;
            }

            if ($argumentWrapper->getType() === null) {
                continue;
            }

            $oldName = $argumentWrapper->getName();
            if ($this->isSplClass($argumentWrapper->getType())) {
                continue;
            }

            $expectedName = $this->getExpectedNameFromType($argumentWrapper->getType());

            if ($oldName === $expectedName) {
                continue;
            }

            $argumentWrapper->changeName($expectedName);
            $changedVariableNames[$oldName] = $expectedName;
        }

        foreach ($changedVariableNames as $oldName => $newName) {
            $methodWrapper->renameEveryVariableOccurrence($oldName, $newName);
        }
    }

    private function getExpectedNameFromType(string $type): string
    {
        $rawName = $type;

        if (Strings::endsWith($rawName, 'Interface')) {
            $rawName = Strings::substring($rawName, 0, - strlen('Interface'));
        }

        if (Strings::startsWith($rawName, 'Abstract')) {
            $rawName = Strings::substring($rawName, strlen('Abstract'));
        }

        if (Strings::startsWith($rawName, 'Spl')) {
            $rawName = Strings::substring($rawName, strlen('Spl'));
        }

        return lcfirst($rawName);
    }

    private function isSplClass(string $class): bool
    {
        return Strings::startsWith($class, 'Spl');
    }
}
