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
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapper;

final class NoInterfaceOnAbstractClassFixer implements FixerInterface, DefinedFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Abstract cannot implement interface, as they do not exists and might flaw design.',
            [
                new CodeSample('
<?php
abstract class AbstractSomeClass implements SomeInterface {};'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_ABSTRACT, T_CLASS, T_IMPLEMENTS]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $tokensReversed = array_reverse(iterator_to_array($tokens), true);

        /** @var Token $token */
        foreach ($tokensReversed as $index => $token) {
            if (! $token->isGivenKind(T_CLASS)) {
                continue;
            }

            $classWrapper = ClassWrapper::createFromTokensArrayStartPosition($tokens, $index);

            if (! $classWrapper->isAbstract()) {
                continue;
            }

            if (! $classWrapper->getInterfaceNames()) {
                continue;
            }

            $classWrapper->clearImplements();
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
}
