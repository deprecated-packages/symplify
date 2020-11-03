<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireThisOnParentMethodCallRule\RequireThisOnParentMethodCallRuleTest
 */
final class RequireThisOnParentMethodCallRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use $this-> on parent method call unless in the same named method';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param StaticCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->class instanceof Name) {
            return [];
        }

        $isParentCall = $node->class->parts[0] === 'parent';
        if (! $isParentCall) {
            return [];
        }

        $classMethod = $this->resolveCurrentClassMethod($node);
        if ($classMethod === null) {
            return [];
        }

        /** @var Identifier $classMethodIdentifier */
        $classMethodIdentifier = $classMethod->name;
        /** @var Identifier $staticCallIdentifier */
        $staticCallIdentifier = $node->name;

        if ((string) $classMethodIdentifier === (string) $staticCallIdentifier) {
            return [];
        }

        if ($this->isMethodNameExistsInCurrentClass($classMethod, (string) $staticCallIdentifier)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isMethodNameExistsInCurrentClass(ClassMethod $classMethod, string $methodName): bool
    {
        $class = $this->resolveCurrentClass($classMethod);
        return $class instanceof Class_ && $class->getMethod($methodName) instanceof ClassMethod;
    }
}
