<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory;

use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symplify\PhpConfigPrinter\Exception\NotImplementedYetException;
use Symplify\PhpConfigPrinter\ExprResolver\StringExprResolver;
use Symplify\PhpConfigPrinter\ExprResolver\TaggedReturnsCloneResolver;
use Symplify\PhpConfigPrinter\ExprResolver\TaggedServiceResolver;

final class ArgsNodeFactory
{
    /**
     * @var string
     */
    private const TAG_SERVICE = 'service';

    /**
     * @var string
     */
    private const TAG_RETURNS_CLONE = 'returns_clone';

    /**
     * @var StringExprResolver
     */
    private $stringExprResolver;

    /**
     * @var TaggedReturnsCloneResolver
     */
    private $taggedReturnsCloneResolver;

    /**
     * @var TaggedServiceResolver
     */
    private $taggedServiceResolver;

    public function __construct(
        StringExprResolver $stringExprResolver,
        TaggedReturnsCloneResolver $taggedReturnsCloneResolver,
        TaggedServiceResolver $taggedServiceResolver
    ) {
        $this->stringExprResolver = $stringExprResolver;
        $this->taggedReturnsCloneResolver = $taggedReturnsCloneResolver;
        $this->taggedServiceResolver = $taggedServiceResolver;
    }

    /**
     * @return Arg[]
     */
    public function createFromValuesAndWrapInArray($values): array
    {
        if (is_array($values)) {
            $array = $this->resolveExprFromArray($values);
        } else {
            $expr = $this->resolveExpr($values);
            $items = [new ArrayItem($expr)];
            $array = new Array_($items);
        }

        return [new Arg($array)];
    }

    /**
     * @return Arg[]
     */
    public function createFromValues(
        $values,
        bool $skipServiceReference = false,
        bool $skipClassesToConstantReference = false
    ): array {
        if (is_array($values)) {
            $args = [];
            foreach ($values as $value) {
                $expr = $this->resolveExpr($value, $skipServiceReference, $skipClassesToConstantReference);
                $args[] = new Arg($expr);
            }

            return $args;
        }

        if ($values instanceof Node) {
            if ($values instanceof Arg) {
                return [$values];
            }

            if ($values instanceof Expr) {
                return [new Arg($values)];
            }
        }

        if (is_string($values)) {
            $expr = $this->resolveExpr($values);
            return [new Arg($expr)];
        }

        throw new NotImplementedYetException();
    }

    public function resolveExpr(
        $value,
        bool $skipServiceReference = false,
        bool $skipClassesToConstantReference = false
    ): Expr {
        if (is_string($value)) {
            return $this->stringExprResolver->resolve(
                $value,
                $skipServiceReference,
                $skipClassesToConstantReference
            );
        }

        if ($value instanceof Expr) {
            return $value;
        }

        if ($value instanceof TaggedValue) {
            return $this->createServiceReferenceFromTaggedValue($value);
        }

        if (is_array($value)) {
            $arrayItems = $this->resolveArrayItems($value, $skipClassesToConstantReference);
            return new Array_($arrayItems);
        }

        return BuilderHelpers::normalizeValue($value);
    }

    private function resolveExprFromArray(array $values): Array_
    {
        $arrayItems = [];
        foreach ($values as $key => $value) {
            $expr = is_array($value) ? $this->resolveExprFromArray($value) : $this->resolveExpr($value);

            if (! is_int($key)) {
                $keyExpr = $this->resolveExpr($key);
                $arrayItem = new ArrayItem($expr, $keyExpr);
            } else {
                $arrayItem = new ArrayItem($expr);
            }

            $arrayItems[] = $arrayItem;
        }

        return new Array_($arrayItems);
    }

    private function createServiceReferenceFromTaggedValue(TaggedValue $taggedValue): Expr
    {
        // that's the only value
        if ($taggedValue->getTag() === self::TAG_RETURNS_CLONE) {
            return $this->taggedReturnsCloneResolver->resolve($taggedValue);
        }

        if ($taggedValue->getTag() === self::TAG_SERVICE) {
            return $this->taggedServiceResolver->resolve($taggedValue);
        }

        $args = $this->createFromValues($taggedValue->getValue());
        return new FuncCall(new Name($taggedValue->getTag()), $args);
    }

    /**
     * @param mixed[] $value
     * @return ArrayItem[]
     */
    private function resolveArrayItems(array $value, bool $skipClassesToConstantReference): array
    {
        $arrayItems = [];

        $naturalKey = 0;
        foreach ($value as $nestedKey => $nestedValue) {
            $valueExpr = $this->resolveExpr($nestedValue, false, $skipClassesToConstantReference);

            if (! is_int($nestedKey) || $nestedKey !== $naturalKey) {
                $keyExpr = $this->resolveExpr($nestedKey, false, $skipClassesToConstantReference);
                $arrayItem = new ArrayItem($valueExpr, $keyExpr);
            } else {
                $arrayItem = new ArrayItem($valueExpr);
            }

            $arrayItems[] = $arrayItem;

            ++$naturalKey;
        }

        return $arrayItems;
    }
}
