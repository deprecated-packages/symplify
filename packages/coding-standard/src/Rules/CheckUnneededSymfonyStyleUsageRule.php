<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\TypeWithClassName;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\CheckUnneededSymfonyStyleUsageRule\CheckUnneededSymfonyStyleUsageRuleTest
 */
final class CheckUnneededSymfonyStyleUsageRule implements Rule
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
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->hasParentClassSymfonyStyle($node)) {
            return [];
        }

        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($node, MethodCall::class);

        $foundAllowedMethod = false;
        foreach ($methodCalls as $methodCall) {
            $callerType = $scope->getType($methodCall->var);
            if (! $callerType instanceof TypeWithClassName) {
                continue;
            }

            if (! is_a($callerType->getClassName(), SymfonyStyle::class, true)) {
                continue;
            }

            if ($methodCall->name instanceof Expr) {
                continue;
            }

            $methodName = (string) $methodCall->name;
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
