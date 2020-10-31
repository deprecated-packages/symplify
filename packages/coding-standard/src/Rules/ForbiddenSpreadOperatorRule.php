<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenSpreadOperatorRule\ForbiddenSpreadOperatorRuleTest
 */
final class ForbiddenSpreadOperatorRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Spread operator is not allowed.';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Arg::class, ClassMethod::class, Function_::class];
    }

    /**
     * @param Arg|ClassMethod|Function_ $node
     * @return mixed[]|string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node instanceof Arg) {
            return $this->processParam($node);
        }

        if ($node->unpack) {
            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    /**
     * @param ClassMethod|Function_ $node
     * @return string[]
     */
    private function processParam(Node $node): array
    {
        $params = $node->params;
        foreach ($params as $param) {
            if ($param->variadic) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }
}
