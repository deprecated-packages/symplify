<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Solid;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;

/**
 * See https://stackoverflow.com/a/9979425/1348344
 *
 * Inspiration http://www.andreybutov.com/2011/08/20/how-do-i-find-unused-functions-in-my-php-project/
 */
final class NoUnusedPublicMethodFixer implements FixerInterface, DefinedFixerInterface, DualRunInterface
{
    /**
     * @var int
     */
    private $runNumber = 1;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Unused public method can be removed.',
            [
                new CodeSample('
<?php
class SomeClass {
    public function someMethod()
    {
    }
};'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_FUNCTION]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $tokensReversed = array_reverse(iterator_to_array($tokens), true);

        /** @var Token $token */
        foreach ($tokensReversed as $index => $token) {
            if (! $token->isGivenKind(T_CLASS)) {
                continue;
            }

            $this->fixClass($tokens, $index);
        }

        $this->runNumber++;
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

    private function fixClass(Tokens $tokens, int $index): void
    {



    }
}
