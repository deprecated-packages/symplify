<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
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
        if (! is_a($scope->getType($node->var)->getClassName(), SymfonyStyle::class, true)) {
            return [];
        }

        $name = strtolower($node->name->name);
        if (! in_array($node->name->name, ['newline', 'write', 'writeln'], true)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
