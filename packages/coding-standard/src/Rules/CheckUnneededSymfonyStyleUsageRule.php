<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;
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
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct()
    {
        $this->nodeFinder = new NodeFinder();
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($node, MethodCall::class);
        $foundAllowedMethod = [];
        foreach ($methodCalls as $methodCall) {
            /** @var Variable $variable */
            $variable = $methodCall->var;
            $objectType = $scope->getType($variable);
            if ($objectType instanceof ObjectType && ! is_a($objectType->getClassName(), SymfonyStyle::class, true)) {
                continue;
            }

            /** @var Identifier $name */
            $name = $methodCall->name;
            $methodName = strtolower((string) $name);
            if (! in_array($methodName, ['newline', 'write', 'writeln'], true)) {
                $foundAllowedMethod = true;
                break;
            }
        }

        if ($foundAllowedMethod) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
