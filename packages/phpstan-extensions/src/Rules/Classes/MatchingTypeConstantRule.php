<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Rules\Classes;

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

final class MatchingTypeConstantRule implements Rule
{
    /**
     * @var string[][]
     */
    private const TYPE_NODES_TO_ACCEPTED_TYPES = [
        LNumber::class => ['int'],
        DNumber::class => ['float', 'double'],
        String_::class => ['string'],
        ConstFetch::class => ['bool'],
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

        $varAnnotations = $this->getVarAnnotationsForNode($node);
        if (count($varAnnotations) === 0) {
            return [];
        }

        $types = $varAnnotations[0]->getNormalizedTypes();
        if (count($types) !== 1) {
            return [];
        }

        $type = $types[0];

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
        $message = sprintf(
            'Constant type should be "%s", but is "%s"',
            $expectedType,
            $this->getStringFromNodeClass($typeNodeClass) // @todo stringify
        );

        return [$message];
    }

    private function getStringFromNodeClass(string $nodeClass): string
    {
        if (is_a($nodeClass, String_::class, true)) {
            return 'string';
        }

        if (is_a($nodeClass, LNumber::class, true)) {
            return 'int';
        }

        if (is_a($nodeClass, DNumber::class, true)) {
            return 'float';
        }

        if (is_a($nodeClass, ConstFetch::class, true)) {
            return 'bool';
        }

        return $nodeClass;
    }
}
