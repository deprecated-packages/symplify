<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\FixerTokenWrapper\DocBlockWrapper;
use Symplify\CodingStandard\FixerTokenWrapper\MethodWrapper;
use Symplify\CodingStandard\Tokenizer\ClassTokensAnalyzer;

final class RemoveUselessDocBlockFixer implements FixerInterface, DefinedFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Block comment should only contain useful information about types.',
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

                $this->processReturnTag($methodWrapper, $docBlockWrapper);
                $this->processParamTag($methodWrapper, $docBlockWrapper);
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

    /**
     * Runs before @see \PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer.
     */
    public function getPriority(): int
    {
        return 10;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function processReturnTag(MethodWrapper $methodWrapper, DocBlockWrapper $docBlockWrapper): void
    {
        $typehintType = $methodWrapper->getReturnType();
        $docBlockType = $docBlockWrapper->getReturnType();

        if ($typehintType === $docBlockType) {
            if ($docBlockWrapper->getReturnTypeDescription()) {
                return;
            }

            $docBlockWrapper->removeReturnType();
        }

        if ($typehintType === null || $docBlockType === null) {
            return;
        }

        if (Strings::contains($typehintType, '|') && Strings::contains($docBlockType, '|')) {
            $this->processReturnTagMultiTypes($typehintType, $docBlockType, $docBlockWrapper);
        }
    }

    private function processParamTag(MethodWrapper $methodWrapper, DocBlockWrapper $docBlockWrapper): void
    {
        foreach ($methodWrapper->getArguments() as $argumentWrapper) {
            $argumentType = $docBlockWrapper->getArgumentType($argumentWrapper->getName());

            $argumentDescription = $docBlockWrapper->getArgumentTypeDescription($argumentWrapper->getName());

            if ($argumentType === null && $argumentDescription === null) {
                $docBlockWrapper->removeParamType($argumentWrapper->getName());

                continue;
            }

            if ($argumentType === $argumentWrapper->getType()) {
                if ($argumentDescription && $this->isDescriptionUseful($argumentDescription, $argumentType)) {
                    continue;
                }

                $docBlockWrapper->removeParamType($argumentWrapper->getName());
            }
        }
    }

    private function isDescriptionUseful(string $description, ?string $type): bool
    {
        if (! $description) {
            return false;
        }

        // improve with additional cases, probably regex
        if ($type && $description === sprintf('A %s instance', $type)) {
            return false;
        }

        return true;
    }

    private function processReturnTagMultiTypes(
        string $docBlockType,
        string $typehintType,
        DocBlockWrapper $docBlockWrapper
    ): void {
        $typehintTypes = explode('|', $typehintType);
        $docBlockTypes = explode('|', $docBlockType);

        if ($docBlockWrapper->getReturnTypeDescription()) {
            return;
        }

        if (sort($typehintTypes) === sort($docBlockTypes)) {
            $docBlockWrapper->removeReturnType();
        }
    }
}
