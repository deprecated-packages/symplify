<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Empty_;
use PhpParser\Node\Expr\Isset_;
use PHPStan\Analyser\Scope;
use PHPStan\Type\TypeWithClassName;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoIssetOnObjectRule\NoIssetOnObjectRuleTest
 */
final class NoIssetOnObjectRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use default null value and nullable compare instead of isset on object';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Empty_::class, Isset_::class];
    }

    /**
     * @param Empty_|Isset_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node instanceof Isset_) {
            return $this->processIsset($node, $scope);
        }

        return $this->processEmpty($node, $scope);
    }

    /**
     * @return string[]
     */
    private function processIsset(Isset_ $isset, Scope $scope): array
    {
        foreach ($isset->vars as $var) {
            if ($this->shouldSkipVariable($var, $scope)) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function processEmpty(Empty_ $empty, Scope $scope): array
    {
        $expr = $empty->expr;

        if ($this->shouldSkipVariable($expr, $scope)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function shouldSkipVariable(Expr $expr, Scope $scope): bool
    {
        if ($expr instanceof ArrayDimFetch) {
            return true;
        }

        $varType = $scope->getType($expr);

        return ! $varType instanceof TypeWithClassName;
    }
}
