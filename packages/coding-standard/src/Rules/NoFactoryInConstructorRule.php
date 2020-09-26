<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoFactoryInConstructorRule\NoFactoryInConstructorRuleTest
 */
final class NoFactoryInConstructorRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use factory in constructor';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ((string) $node->name !== '__construct') {
            return [];
        }

        /** @var Stmt[] $stmts */
        $stmts = $node->getStmts();
        if ($stmts === []) {
            return [];
        }

        foreach ($stmts as $stmt) {
            if (! $stmt instanceof Expression) {
                continue;
            }

            $expression = $stmt->expr;
            while ($expression) {
                if ($expression instanceof MethodCall && ! in_array(
                    /** @phpstan-ignore-next-line */
                    $expression->var->name,
                    ['this', 'self', 'static'],
                    true
                )) {
                    return [self::ERROR_MESSAGE];
                }

                /** @phpstan-ignore-next-line */
                $expression = $expression->expr;
            }
        }

        return [];
    }
}
