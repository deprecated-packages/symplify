<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\TokenRunner\DocBlock\DescriptionAnalyzer;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ClassWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\DocBlockWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\MethodWrapper;

final class RemoveUselessDocBlockFixer implements FixerInterface, DefinedFixerInterface, WhitespacesAwareFixerInterface, ConfigurationDefinitionFixerInterface
{
    /**
     * @var string
     */
    public const USEFUL_TYPES_OPTION = 'useful_types';

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    /**
     * @var mixed[]
     */
    private $configuration = [];

    public function __construct()
    {
        // set defaults
        $this->configuration = $this->getConfigurationDefinition()
            ->resolve([]);
    }

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

            $classWrapper = ClassWrapper::createFromTokensArrayStartPosition($tokens, $index);
            foreach ($classWrapper->getMethodWrappers() as $methodWrapper) {
                $docBlockWrapper = $methodWrapper->getDocBlockWrapper();
                if ($docBlockWrapper === null) {
                    continue;
                }

                $docBlockWrapper->setWhitespacesFixerConfig($this->whitespacesFixerConfig);

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

    public function setWhitespacesConfig(WhitespacesFixerConfig $whitespacesFixerConfig): void
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    /**
     * @param mixed[]|null $configuration
     */
    public function configure(?array $configuration = null): void
    {
        if ($configuration === null) {
            return;
        }

        $this->configuration = $this->getConfigurationDefinition()
            ->resolve($configuration);
    }

    public function getConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $option = (new FixerOptionBuilder(self::USEFUL_TYPES_OPTION, 'List of useful types to allow.'))
            ->setDefault([])
            ->getOption();

        return new FixerConfigurationResolver([$option]);
    }

    private function processReturnTag(MethodWrapper $methodWrapper, DocBlockWrapper $docBlockWrapper): void
    {
        $typehintType = $methodWrapper->getReturnType();
        $docBlockType = $docBlockWrapper->getReturnType();

        if ($typehintType === null || $docBlockType === null) {
            return;
        }

        if ($typehintType === $docBlockType) {
            if ($docBlockWrapper->getReturnTypeDescription()) {
                return;
            }

            $docBlockWrapper->removeReturnType();
        }

        if (Strings::contains($typehintType, '|') && Strings::contains($docBlockType, '|')) {
            $this->processReturnTagMultiTypes($typehintType, $docBlockType, $docBlockWrapper);
        }

        if ($typehintType && Strings::endsWith($typehintType, '\\' . $docBlockWrapper->getReturnType())) {
            $docBlockWrapper->removeReturnType();
            return;
        }

        // simple types
        if ($docBlockType === 'boolean' && $typehintType === 'bool') {
            $docBlockWrapper->removeReturnType();
        }
    }

    private function processParamTag(MethodWrapper $methodWrapper, DocBlockWrapper $docBlockWrapper): void
    {
        foreach ($methodWrapper->getArguments() as $argumentWrapper) {
            $docBlockType = $docBlockWrapper->getArgumentType($argumentWrapper->getName());
            $argumentDescription = $docBlockWrapper->getArgumentTypeDescription($argumentWrapper->getName());

            $isDescriptionUseful = (new DescriptionAnalyzer())->isDescriptionUseful(
                (string) $argumentDescription,
                $docBlockType,
                $argumentWrapper->getName()
            );

            if ($isDescriptionUseful === true) {
                continue;
            }

            if ($docBlockType === $argumentDescription) {
                $docBlockWrapper->removeParamType($argumentWrapper->getName());
                continue;
            }

            if ($this->shouldSkip($docBlockType, $argumentDescription)) {
                continue;
            }

            if ($docBlockType === 'mixed' && $isDescriptionUseful === false) {
                $docBlockWrapper->removeParamType($argumentWrapper->getName());
                continue;
            }

            if ($docBlockType === $argumentWrapper->getType()) {
                $docBlockWrapper->removeParamType($argumentWrapper->getName());
                continue;
            }

            if ($docBlockType && Strings::endsWith($docBlockType, '\\' . $argumentWrapper->getType())) {
                $docBlockWrapper->removeParamType($argumentWrapper->getName());
                continue;
            }

            // simple types
            if ($docBlockType === 'boolean' && $argumentWrapper->getType() === 'bool') {
                $docBlockWrapper->removeParamType($argumentWrapper->getName());
            }
        }

        $this->removeUnpresentTags($docBlockWrapper, $methodWrapper);
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

        sort($typehintTypes);
        sort($docBlockTypes);

        if ($typehintTypes === $docBlockTypes) {
            $docBlockWrapper->removeReturnType();
        }
    }

    private function shouldSkip(?string $docBlockType, ?string $argumentDescription): bool
    {
        if ($argumentDescription === null || $docBlockType === null) {
            return true;
        }

        // is array specification - keep it
        if (Strings::contains($docBlockType, '[]')) {
            return true;
        }

        // is intersect type specification, but not nullable - keep it
        if (Strings::contains($docBlockType, '|') && ! Strings::contains($docBlockType, 'null')) {
            return true;
        }

        return false;
    }

    private function removeUnpresentTags(DocBlockWrapper $docBlockWrapper, MethodWrapper $methodWrapper): void
    {
        $argumentNames = $methodWrapper->getArgumentNames();

        foreach ($docBlockWrapper->getParamTags() as $paramTag) {
            if (in_array($paramTag->getVariableName(), $argumentNames)) {
                continue;
            }

            $docBlockWrapper->removeParamType($paramTag->getVariableName());
        }
    }
}
