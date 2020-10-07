<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Node\ClassMethodsNode;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\CheckUnneededSymfonyStyleUsageRule\CheckUnneededSymfonyStyleUsageRuleTest
 */
final class CheckUnneededSymfonyStyleUsageRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'SymfonyStyle usage is unneeded for only newline, write, and/or writeln, use PHP_EOL and concatenation instead';

    /**
     * @var string[]
     */
    private const SIMPLE_CONSOLE_OUTPUT_METHODS = ['newline', 'write', 'writeln'];

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethodsNode::class];
    }

    /**
     * @param ClassMethodsNode $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var ClassLike $classLike */
        $classLike = $node->getClass();
        if ($this->hasParentClassSymfonyStyle($classLike)) {
            return [];
        }

        $methodCalls = $node->getMethodCalls();
        foreach ($methodCalls as $methodCall) {
            /** @var MethodCall $methodCallNode */
            $methodCallNode = $methodCall->getNode();
            if (! $methodCallNode->var instanceof Expr) {
                return [];
            }

            $callerType = $methodCall->getScope()
                ->getType($methodCallNode->var);
            if (! method_exists($callerType, 'getClassName')) {
                return [];
            }

            if (! is_a($callerType->getClassName(), SymfonyStyle::class, true)) {
                return [];
            }

            /** @var Identifier $methodCallIdentifier */
            $methodCallIdentifier = $methodCallNode->name;
            $methodName = (string) $methodCallIdentifier->name;
            if (! in_array($methodName, self::SIMPLE_CONSOLE_OUTPUT_METHODS, true)) {
                return [];
            }
        }

        if ($methodCalls === []) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function hasParentClassSymfonyStyle(ClassLike $classLike): bool
    {
        if (! $classLike instanceof Class_) {
            return false;
        }

        if ($classLike->extends === null) {
            return false;
        }

        $parentClass = $classLike->extends->toString();

        return is_a($parentClass, SymfonyStyle::class, true);
    }
}
