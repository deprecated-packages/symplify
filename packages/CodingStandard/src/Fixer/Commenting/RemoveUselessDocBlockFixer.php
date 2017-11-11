<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Tokenizer\ClassTokensAnalyzer;

final class RemoveUselessDocBlockFixer implements FixerInterface, DefinedFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Block comment should only contain useful information.',
            [
                new CodeSample('<?php
/**
 * @return int 
 */
public function getCount(): int
{
}
'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_FUNCTION, T_DOC_COMMENT]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = count($tokens) - 1; $index > 1; --$index) {
            $token = $tokens[$index];

            if (! $token->isGivenKind(T_CLASS)) {
                continue;
            }

            $classTokensAnalyzer = ClassTokensAnalyzer::createFromTokensArrayStartPosition($tokens, $index);
            foreach ($classTokensAnalyzer->getMethodWrappers() as $methodWrapper) {
                $docBlockWrapper = $methodWrapper->getDocBlockWrapper();
                if ($docBlockWrapper === null) {
                    continue;
                }

                // 1. process return tag
                if ($methodWrapper->getReturnType() === $docBlockWrapper->getReturnType()) {
                    if ($docBlockWrapper->getReturnTypeDescription()) {
                        continue;
                    }

                    $docBlockWrapper->removeReturnType();
                }

                // 2. process param tag
                foreach ($methodWrapper->getArguments() as $argumentWrapper) {
                    $argumentType = $docBlockWrapper->getArgumentType($argumentWrapper->getName());
                    if ($argumentType === $argumentWrapper->getType()) {
                        $docBlockWrapper->removeParamType($argumentWrapper->getName());
                    }
                }
            }
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
        // before empty doc block cleaner
        return 0;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }
}
