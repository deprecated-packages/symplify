<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use SplFileInfo;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeAnalyzer;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\DocBlock\DescriptionAnalyzer;
use Symplify\CodingStandard\TokenRunner\DocBlock\ParamAndReturnTagAnalyzer;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\DocBlockWrapper;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\MethodWrapper;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\MethodWrapperFactory;

final class RemoveUselessDocBlockFixer extends AbstractSymplifyFixer implements ConfigurableFixerInterface
{
    /**
     * @var DescriptionAnalyzer
     */
    private $descriptionAnalyzer;

    /**
     * @var ParamAndReturnTagAnalyzer
     */
    private $paramAndReturnTagAnalyzer;

    /**
     * @var MethodWrapperFactory
     */
    private $methodWrapperFactory;

    /**
     * @var TypeNodeAnalyzer
     */
    private $typeNodeAnalyzer;

    public function __construct(
        DescriptionAnalyzer $descriptionAnalyzer,
        ParamAndReturnTagAnalyzer $paramAndReturnTagAnalyzer,
        MethodWrapperFactory $methodWrapperFactory,
        TypeNodeAnalyzer $typeNodeAnalyzer
    ) {
        sleep(3);

        $message = sprintf(
            '%s" is deprecated.%sUse "%s" instead.',
            self::class,
            PHP_EOL,
            NoSuperfluousPhpdocTagsFixer::class
        );
        trigger_error($message, E_USER_DEPRECATED);

        $this->descriptionAnalyzer = $descriptionAnalyzer;
        $this->paramAndReturnTagAnalyzer = $paramAndReturnTagAnalyzer;
        $this->methodWrapperFactory = $methodWrapperFactory;
        $this->typeNodeAnalyzer = $typeNodeAnalyzer;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Block comment should only contain useful information about types.',
            [new CodeSample('<?php
/**
 * @return int 
 */
public function getCount(): int
{
}
')]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_FUNCTION, T_DOC_COMMENT]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->reverseTokens($tokens) as $index => $token) {
            $token = $tokens[$index];
            if (! $this->isNamedFunctionToken($tokens, $token, $index)) {
                continue;
            }

            $methodWrapper = $this->methodWrapperFactory->createFromTokensAndPosition($tokens, $index);

            $docBlockWrapper = $methodWrapper->getDocBlockWrapper();
            if ($docBlockWrapper === null) {
                continue;
            }

            $this->processReturnTag($methodWrapper, $docBlockWrapper);
            $this->processParamTag($methodWrapper, $docBlockWrapper);
            $this->removeTagForMissingParameters($methodWrapper, $docBlockWrapper);

            $docBlockWrapper->saveNewPhpDocInfo();
        }
    }

    public function getPriority(): int
    {
        return $this->getPriorityBefore(PhpdocIndentFixer::class);
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(?array $configuration = null): void
    {
        $this->paramAndReturnTagAnalyzer->setUselessTypes($configuration['useless_types'] ?? []);
    }

    private function isNamedFunctionToken(Tokens $tokens, Token $token, int $index): bool
    {
        if (! $token->isGivenKind(T_FUNCTION)) {
            return false;
        }

        $possibleNamePosition = $tokens->getNextMeaningfulToken($index);

        $possibleNameToken = $tokens[$possibleNamePosition];

        return $possibleNameToken->isGivenKind(T_STRING);
    }

    private function processReturnTag(MethodWrapper $methodWrapper, DocBlockWrapper $docBlockWrapper): void
    {
        $returnTagValue = $docBlockWrapper->getPhpDocInfo()->getReturnTagValue();
        if ($returnTagValue === null) {
            return;
        }

        $typehintTypes = $methodWrapper->getReturnTypes();
        $returnTypes = $docBlockWrapper->getPhpDocInfo()->getReturnTypes();

        $isDescriptionUseful = $this->descriptionAnalyzer->isDescriptionUseful(
            $returnTagValue->description,
            $returnTagValue->type,
            null
        );

        if ($this->isUselessNullableTypehint($typehintTypes, $returnTypes) && $isDescriptionUseful === false) {
            $docBlockWrapper->removeReturnType();
            return;
        }

        if ($this->paramAndReturnTagAnalyzer->isTagUseful(
            $returnTagValue->type,
            $returnTagValue->description,
            $typehintTypes
        )) {
            return;
        }

        if ($isDescriptionUseful) {
            return;
        }

        $docBlockWrapper->removeReturnType();
    }

    private function processParamTag(MethodWrapper $methodWrapper, DocBlockWrapper $docBlockWrapper): void
    {
        foreach ($methodWrapper->getArguments() as $argumentWrapper) {
            $typehintType = $argumentWrapper->getTypes();
            $typeNode = $docBlockWrapper->getArgumentTypeNode($argumentWrapper->getName());

            $docDescription = $docBlockWrapper->getParamTagDescription($argumentWrapper->getName());

            $isDescriptionUseful = $this->descriptionAnalyzer->isDescriptionUseful(
                $docDescription,
                $typeNode,
                $argumentWrapper->getName()
            );

            if ($isDescriptionUseful === true) {
                continue;
            }

            // no description, no typehint, just property name
            if ($typeNode === null) {
                $docBlockWrapper->removeParamType($argumentWrapper->getName());
                continue;
            }

            if ($this->shouldSkip($typeNode, $docDescription)) {
                continue;
            }

            if (! $this->paramAndReturnTagAnalyzer->isTagUseful($typeNode, $docDescription, $typehintType)) {
                $docBlockWrapper->removeParamType($argumentWrapper->getName());
            }
        }
    }

    private function removeTagForMissingParameters(MethodWrapper $methodWrapper, DocBlockWrapper $docBlockWrapper): void
    {
        $argumentNames = $methodWrapper->getArgumentNames();

        foreach ($docBlockWrapper->getPhpDocInfo()->getParamTagValues() as $paramTagValue) {
            if (in_array(ltrim($paramTagValue->parameterName, '$'), $argumentNames, true)) {
                continue;
            }

            $docBlockWrapper->removeParamType($paramTagValue->parameterName);
        }
    }

    /**
     * @param string[] $returnTypehintTypes
     * @param string[] $returnDocTypes
     */
    private function isUselessNullableTypehint(array $returnTypehintTypes, array $returnDocTypes): bool
    {
        if (count($returnTypehintTypes) !== count($returnDocTypes)) {
            return false;
        }

        return count(array_intersect($returnDocTypes, $returnTypehintTypes)) === count($returnTypehintTypes);
    }

    private function shouldSkip(?TypeNode $typeNode, ?string $argumentDescription): bool
    {
        if ($argumentDescription === null || $typeNode === null) {
            return true;
        }

        if ($this->typeNodeAnalyzer->containsArrayType($typeNode)) {
            return true;
        }

        return $this->typeNodeAnalyzer->isIntersectionAndNotNullable($typeNode);
    }
}
