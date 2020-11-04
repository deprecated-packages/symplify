<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\CheckConstantExpressionDefinedInConstructOrSetupRuleTest
 */
final class CheckConstantExpressionDefinedInConstructOrSetupRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constant expression should only defined in __construct() or setUp()';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Assign::class];
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->expr instanceof ClassConstFetch) {
            return [];
        }

        $classMethod = $this->resolveCurrentClassMethod($node);
        if ($classMethod === null) {
            return [];
        }

        if (in_array(strtolower((string) $classMethod->name), ['__construct', 'setup'], true)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
