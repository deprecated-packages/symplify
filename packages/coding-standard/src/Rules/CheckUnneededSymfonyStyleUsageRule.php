<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
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
     * @param Class_|ClassMethodsNode $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->hasParentClassSymfonyStyle($node->getClass())) {
            return [];
        }

        $foundAllowedMethod = false;
        $methodCalls = $node->getMethodCalls();
        foreach ($methodCalls as $methodCall) {
            $methodCallNode = $methodCall->getNode();
            $callerType = $methodCall->getScope()
                ->getType($methodCallNode->var);

            if (! method_exists($callerType, 'getClassName')) {
                $foundAllowedMethod = true;
                break;
            }

            if (! is_a($callerType->getClassName(), SymfonyStyle::class, true)) {
                $foundAllowedMethod = true;
                break;
            }

            $methodName = (string) $methodCallNode->name;
            if (! in_array($methodName, self::SIMPLE_CONSOLE_OUTPUT_METHODS, true)) {
                $foundAllowedMethod = true;
                break;
            }
        }

        if ($foundAllowedMethod) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function hasParentClassSymfonyStyle(Class_ $class): bool
    {
        if ($class->extends === null) {
            return false;
        }

        $parentClass = $class->extends->toString();

        return is_a($parentClass, SymfonyStyle::class, true);
    }
}
