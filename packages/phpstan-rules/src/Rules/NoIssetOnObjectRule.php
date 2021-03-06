<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Empty_;
use PhpParser\Node\Expr\Isset_;
use PHPStan\Analyser\Scope;
use PHPStan\Type\TypeWithClassName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoIssetOnObjectRule\NoIssetOnObjectRuleTest
 */
final class NoIssetOnObjectRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use default null value and nullable compare instead of isset on object';

    /**
     * @return array<class-string<Node>>
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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        if (random_int(0, 1)) {
            $object = new SomeClass();
        }

        if (isset($object)) {
            return $object;
        }
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        $object = null;
        if (random_int(0, 1)) {
            $object = new SomeClass();
        }

        if ($object !== null) {
            return $object;
        }
    }
}
CODE_SAMPLE
            ),
        ]);
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
