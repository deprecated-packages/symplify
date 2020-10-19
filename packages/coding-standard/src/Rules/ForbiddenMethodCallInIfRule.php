<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenMethodCallInIfRule\ForbiddenMethodCallInIfRuleTest
 */
final class ForbiddenMethodCallInIfRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method call in if or elseif is not allowed.';

    /**
     * @var string[]
     */
    private const BOOL_PREFIXES = [
        'is',
        'are',
        'was',
        'will',
        'has',
        'have',
        'had',
        'do',
        'does',
        'di',
        'can',
        'could',
        'should',
        'starts',
        'contains',
        'ends',
        'exists',
        'supports',
        'provide',
        # array access
        'offsetExists',
    ];

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [If_::class, ElseIf_::class];
    }

    /**
     * @param If_|ElseIf_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var MethodCall[] $calls */
        $calls = $this->nodeFinder->findInstanceOf($node->cond, MethodCall::class);
        $isHasArgs = $this->isHasArgs($calls, $scope);

        if (! $isHasArgs) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @param MethodCall[] $calls
     */
    private function isHasArgs(array $calls, Scope $scope): bool
    {
        foreach ($calls as $call) {
            if ($call->args === []) {
                continue;
            }

            /** @var ObjectType $type */
            $type = $scope->getType($call->var);

            if ($call->var instanceof PropertyFetch) {
                /** @var ObjectType|ThisType $type */
                $type = $scope->getType($call->var);
            }

            if ($type instanceof ThisType) {
                continue;
            }

            if ($call->name instanceof Identifier && $this->isMethodNameMatchingBoolPrefixes($call->name->toString())) {
                continue;
            }

            return true;
        }

        return false;
    }

    private function isMethodNameMatchingBoolPrefixes(string $methodName): bool
    {
        $prefixesPattern = '#^(' . implode('|', self::BOOL_PREFIXES) . ')#';

        return (bool) Strings::match($methodName, $prefixesPattern);
    }
}
