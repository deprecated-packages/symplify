<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PhpParser\NodeFinder;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenNewInMethodRule\ForbiddenNewInMethodRuleTest
 */
final class ForbiddenNewInMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"new" in method "%s->%s()" is not allowed.';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var array<string, string[]>
     */
    private $forbiddenClassMethods = [];

    /**
     * @param array<string, string[]> $forbiddenClassMethods
     */
    public function __construct(NodeFinder $nodeFinder, array $forbiddenClassMethods = [])
    {
        $this->nodeFinder = $nodeFinder;
        $this->forbiddenClassMethods = $forbiddenClassMethods;
    }

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
        /** @var string $className */
        $className = $node->getClassName();
        if ($className == null) {
            return [];
        }

        /** @var Identitifier $methodIdentifier */
        $methodIdentifier = $node->name;
        $methodName = (string) $methodIdentifier;

        foreach ($this->forbiddenClassMethods as $class => $methods) {
            if ($class !== $className) {
                continue;
            }

            if (in_array($methodName, $methods, true)) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }
}
