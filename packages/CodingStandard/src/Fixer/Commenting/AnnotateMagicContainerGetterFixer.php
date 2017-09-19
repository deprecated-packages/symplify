<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class AnnotateMagicContainerGetterFixer implements FixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Variables created with $container->get(SomeService::class) should have annotation, so every IDE supports autocomplete without any plugins.',
            [
                new CodeSample('<?php
$variable = $container->get(SomeType::class);
'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_VARIABLE]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = count($tokens) - 1; $index > 1; --$index) {
            $token = $tokens[$index];

            if (! $token->isGivenKind(T_CLASS)) {
                continue;
            }

            // ...

            dump($token);
            die;
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
