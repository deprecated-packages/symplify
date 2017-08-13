<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Php;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class ClassStringToClassConstantFixer implements DefinedFixerInterface
{
    /**
     * @var string
     */
    private const CLASS_OR_INTERFACE_PATTERN = '#^[A-Z]\w*[a-z]\w*(\\\\[A-Z]\w*[a-z]\w*)+\z#';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            '`::class` references should be used over string for classes and interfaces.',
            [
                new CodeSample('<?php $className = "DateTime";'),
                new CodeSample('<?php $interfaceName = "DateTimeInterface";'),
                new CodeSample('<?php $interfaceName = "Nette\Utils\DateTime";'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_CONSTANT_ENCAPSED_STRING);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var Token[] $revertedTokens */
        $revertedTokens = array_reverse($tokens->toArray(), true);

        foreach ($revertedTokens as $index => $token) {
            if (! $token->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }

            // remove quotes "" around the string
            $potentialClassInterfaceOrTrait = substr($token->getContent(), 1, -1);
            if (! $this->isClassInterfaceOrTrait($potentialClassInterfaceOrTrait)) {
                continue;
            }

            unset($tokens[$index]);
            $tokens->insertAt($index, $this->convertNameToTokens($potentialClassInterfaceOrTrait));
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
        // run before the OrderedImportsFixer, after the NoLeadingImportSlashFixer
        return -25;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function isClassInterfaceOrTrait(string $potentialClassInterfaceOrTrait): bool
    {
        // lowercase string are not classes; because class_exists() is case-insensitive
        if (ctype_lower($potentialClassInterfaceOrTrait[0])) {
            return false;
        }

        return class_exists($potentialClassInterfaceOrTrait)
            || interface_exists($potentialClassInterfaceOrTrait)
            || trait_exists($potentialClassInterfaceOrTrait)
            || (bool) preg_match(self::CLASS_OR_INTERFACE_PATTERN, $potentialClassInterfaceOrTrait);
    }

    /**
     * @return Token[]
     */
    private function convertNameToTokens(string $classInterfaceOrTraitName): array
    {
        $tokens = [];

        $parts = explode('\\', $classInterfaceOrTraitName);
        foreach ($parts as $part) {
            $tokens[] = new Token([T_NS_SEPARATOR, '\\']);
            $tokens[] = new Token([T_STRING, $part]);
        }

        $tokens[] = new Token([T_DOUBLE_COLON,'::']);
        $tokens[] = new Token([CT::T_CLASS_CONSTANT, 'class']);

        return $tokens;
    }
}
