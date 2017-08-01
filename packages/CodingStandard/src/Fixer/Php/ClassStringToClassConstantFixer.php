<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Php;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
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
            $potentialClassOrInterface = substr($token->getContent(), 1, -1);
            if (! $this->isClassOrInterface($potentialClassOrInterface)) {
                continue;
            }

            $token->clear();
            $tokens->insertAt($index, $this->convertClassOrInterfaceNameToTokens($potentialClassOrInterface));
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
        // should be run before the OrderedImportsFixer, after the NoLeadingImportSlashFixer
        return -25;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function isClassOrInterface(string $potentialClassOrInterface): bool
    {
        // exception for often used "error" string; because class_exists() is case-insensitive
        if ($potentialClassOrInterface === 'error') {
            return false;
        }

        return class_exists($potentialClassOrInterface)
            || interface_exists($potentialClassOrInterface)
            || (bool) preg_match(self::CLASS_OR_INTERFACE_PATTERN, $potentialClassOrInterface);
    }

    private function convertClassOrInterfaceNameToTokens(string $potentialClassOrInterface): Tokens
    {
        $tokens = Tokens::fromCode(sprintf(
            '<?php echo \\%s::class;',
            $potentialClassOrInterface
        ));

        $tokens->clearRange(0, 2); // clear start "<?php"
        $tokens[$tokens->getSize() - 1]->clear(); // clear end ";"
        $tokens->clearEmptyTokens();

        return $tokens;
    }
}
