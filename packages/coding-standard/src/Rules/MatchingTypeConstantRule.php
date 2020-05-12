<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\MatchingTypeConstantRule\MatchingTypeConstantRuleTest
 */
final class MatchingTypeConstantRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constant type should be "%s", but is "%s"';

    /**
     * @var string[][]
     */
    private const TYPE_NODES_TO_ACCEPTED_TYPES = [
        LNumber::class => ['int'],
        DNumber::class => ['float', 'double'],
        String_::class => ['string'],
        ConstFetch::class => ['bool'],
    ];

    /**
     * @var string[]
     */
    private const TYPE_CLASS_TO_STRING_TYPE = [
        String_::class => 'string',
        LNumber::class => 'int',
        DNumber::class => 'float',
        ConstFetch::class => 'bool',
    ];

    public function getNodeType(): string
    {
        return ClassConst::class;
    }

    /**
     * @param ClassConst $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($node)) {
            return [];
        }

        $type = $this->resolveOnlyVarAnnotationType($node);
        if ($type === null) {
            return [];
        }

        // array, unable to resolve?
        if (Strings::endsWith($type, '[]')) {
            return [];
        }

        $constantValue = $node->consts[0]->value;

        return $this->processConstantValue($constantValue, $type);
    }

    private function shouldSkip(ClassConst $classConst): bool
    {
        if ($classConst->getDocComment() === null) {
            return true;
        }

        return count($classConst->consts) !== 1;
    }

    private function resolveOnlyVarAnnotationType(ClassConst $classConst): ?string
    {
        $varAnnotations = $this->getVarAnnotationsForNode($classConst);
        if (count($varAnnotations) === 0) {
            return null;
        }

        $types = $varAnnotations[0]->getNormalizedTypes();
        if (count($types) !== 1) {
            return null;
        }

        return $types[0];
    }

    /**
     * @return string[]
     */
    private function processConstantValue(Expr $expr, string $type): array
    {
        foreach (self::TYPE_NODES_TO_ACCEPTED_TYPES as $typeNode => $acceptedTypes) {
            /** @var string $typeNode */
            if (! is_a($expr, $typeNode, true)) {
                continue;
            }

            if ($this->isValidConstantValue($expr, $type, $acceptedTypes)) {
                return [];
            }

            return $this->reportMissmatch($type, $typeNode);
        }

        return [];
    }

    /**
     * @return Annotation[]
     */
    private function getVarAnnotationsForNode(Node $node): array
    {
        if ($node->getDocComment() === null) {
            return [];
        }

        $docBlock = new DocBlock($node->getDocComment()->getText());

        return $docBlock->getAnnotationsOfType('var');
    }

    /**
     * @param string[] $acceptedTypes
     */
    private function isValidConstantValue(Expr $expr, string $type, array $acceptedTypes): bool
    {
        if (in_array($type, $acceptedTypes, true)) {
            return true;
        }

        // special bool case
        if (! $expr instanceof ConstFetch) {
            return false;
        }

        if ($type !== 'bool') {
            return false;
        }

        return in_array($expr->name->toLowerString(), ['false', 'true'], true);
    }

    /**
     * @return string[]
     */
    private function reportMissmatch(string $expectedType, string $typeNodeClass): array
    {
        $message = sprintf(self::ERROR_MESSAGE, $expectedType, $this->getStringFromNodeClass($typeNodeClass));

        return [$message];
    }

    private function getStringFromNodeClass(string $nodeClass): string
    {
        foreach (self::TYPE_CLASS_TO_STRING_TYPE as $typeClass => $stringType) {
            /** @var string $typeClass */
            if (! is_a($nodeClass, $typeClass, true)) {
                continue;
            }

            return $stringType;
        }

        return $nodeClass;
    }
}
