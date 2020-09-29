<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
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
    public const ERROR_MESSAGE = 'SymfonyStyle is unneeded for only newline, write, and/or writeln, use PHP_EOL and concatenation instead';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        /** @var ObjectType $objectType */
        $objectType = $scope->getType($node->var);
        if (! is_a($objectType->getClassName(), SymfonyStyle::class, true)) {
            return [];
        }

        /** @var Identifier $name */
        $name = $node->name;
        $methodName = strtolower((string) $name);
        if (! in_array($methodName, ['newline', 'write', 'writeln'], true)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
