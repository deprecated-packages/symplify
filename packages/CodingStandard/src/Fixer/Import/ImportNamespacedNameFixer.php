<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Import;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\FixerTokenWrapper\Naming\ClassFqnResolver;
use Symplify\CodingStandard\FixerTokenWrapper\Naming\Name;
use Symplify\CodingStandard\FixerTokenWrapper\Naming\NameAnalyzer;

/**
 * Possible cases.
 *
 * - 1. string that start with pre slash \SomeThing
 * - 2. namespace with conflicts \First\SomeClass + \Second\SomeClass
 */
final class ImportNamespacedNameFixer implements FixerInterface
{
    /**
     * @var int
     */
    private $namespacePosition;

    /**
     * @var string[]
     */
    private $importedNames = [];

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_STRING]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $this->importedNames = [];

        for ($index = $tokens->getSize() - 1; $index > 0; --$index) {
            $token = $tokens[$index];

            // Case 1.
            if (! NameAnalyzer::isImportableName($tokens, $token, $index)) {
                continue;
            }

            $name = ClassFqnResolver::resolveDataFromEnd($tokens, $index);

            $name = $this->uniquateLastPart($name);

            // replace with last name part
            $tokens->overrideRange($name->getStart(), $name->getEnd(), [$name->getLastNameToken()]);

            // has this been already imported?
            if ($this->wasNameImported($name)) {
                continue;
            }

            // add use statement
            $this->addIntoUseStatements($tokens, $name);
        }
    }

    public function getName(): string
    {
        return self::class;
    }

    /**
     * Run before @see \PhpCsFixer\Fixer\Import\OrderedImportsFixer.
     */
    public function getPriority(): int
    {
        return -40;
    }

    public function isRisky(): bool
    {
        // first version is unable to deal with duplicated names
        return true;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function getNamespacePosition(Tokens $tokens): int
    {
        if ($this->namespacePosition) {
            return $this->namespacePosition;
        }

        $namespace = $tokens->findGivenKind(T_NAMESPACE);
        reset($namespace);

        return $this->namespacePosition = key($namespace);
    }

    /**
     * @param Token[] $nameTokens
     */
    private function addIntoUseStatements(Tokens $tokens, Name $name): void
    {
        $namespacePosition = $this->getNamespacePosition($tokens);
        $namespaceSemicolonPosition = $tokens->getNextTokenOfKind($namespacePosition, [';']);

        $tokens->insertAt($namespaceSemicolonPosition + 2, $name->getUseNameTokens());
    }

    private function wasNameImported(Name $name): bool
    {
        if (isset($this->importedNames[$name->getName()])) {
            return true;
        }

        $this->importedNames[$name->getName()] = $name->getLastName();

        return false;
    }

    private function uniquateLastPart(Name $name): Name
    {
        foreach ($this->importedNames as $fullName => $lastName) {
            if ($lastName === $name->getLastName() && $fullName !== $name->getName()) {
                // @todo: make "Second" configurable
                $name->addAlias('Second' . $name->getLastName());
            }
        }

        return $name;
    }
}
