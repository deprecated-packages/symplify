<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Naming;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\Basic\Psr4Fixer;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff;
use function Safe\sleep;
use function Safe\sprintf;

final class ExceptionNameFixer implements DefinedFixerInterface
{
    public function __construct()
    {
        trigger_error(
            sprintf(
                '"%s" was deprecated and will be removed in Symplify\CodingStandard 5.0. Use "%s" instead."',
                self::class,
                ClassNameSuffixByParentSniff::class
            ),
            E_USER_DEPRECATED
        );
        sleep(3); // inspired at "deprecated interface" Tweet
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Exception classes should have suffix "Exception".', [
            new CodeSample('<?php
class SomeClass extends Exception
{
}'),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_EXTENDS, T_STRING]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(T_EXTENDS)) {
                continue;
            }

            if (! $this->isException($tokens, $index)) {
                continue;
            }

            $classNamePosition = (int) $tokens->getPrevMeaningfulToken($index);
            $classNameToken = $tokens[$classNamePosition];
            if (Strings::endsWith($classNameToken->getContent(), 'Exception')) {
                continue;
            }

            $this->fixClassName($tokens, $classNamePosition, $classNameToken->getContent());
        }
    }

    /**
     * Run before @see Psr4Fixer fixer to fix-up file names if needed.
     */
    public function getPriority(): int
    {
        return -5;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function isRisky(): bool
    {
        return true;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function isException(Tokens $tokens, int $index): bool
    {
        $parentClassName = $this->getParentClassName($tokens, $index);

        return Strings::endsWith($parentClassName, 'Exception');
    }

    private function getParentClassName(Tokens $tokens, int $index): string
    {
        $parentClassNamePosition = $tokens->getNextMeaningfulToken($index);
        $parentClassNameToken = $tokens[$parentClassNamePosition];

        return $parentClassNameToken->getContent();
    }

    private function fixClassName(Tokens $tokens, int $classNamePosition, string $oldClassName): void
    {
        $tokens[$classNamePosition] = new Token([T_STRING, $oldClassName . 'Exception']);
    }
}
