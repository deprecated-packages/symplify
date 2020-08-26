<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\PhpParser\NodeNameResolver;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenArrayDestructRule\ForbiddenArrayDestructRuleTest
 */
final class ForbiddenArrayDestructRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Array destruct is not allowed. Use value object to pass data instead';

    /**
     * @var NodeNameResolver
     */
    private $nodeNameResolver;

    public function __construct(NodeNameResolver $nodeNameResolver)
    {
        $this->nodeNameResolver = $nodeNameResolver;
    }

    public function getNodeType(): string
    {
        return Assign::class;
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->var instanceof Array_) {
            return [];
        }

        // swaps are allowed
        if ($node->expr instanceof Array_) {
            return [];
        }

        // "explode()" is allowed
        if ($node->expr instanceof FuncCall && $this->nodeNameResolver->isName($node->expr->name, 'explode')) {
            return [];
        }

        // Strings::split() is allowed
        if ($node->expr instanceof StaticCall && $this->nodeNameResolver->isName($node->expr->name, 'split')) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
