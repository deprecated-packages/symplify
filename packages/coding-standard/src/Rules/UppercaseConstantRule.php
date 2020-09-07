<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\UppercaseConstantRule\UppercaseConstantRuleTest
 */
final class UppercaseConstantRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constant "%s" must be uppercase';

    public function getNodeType(): string
    {
        return ClassConst::class;
    }

    /**
     * @param ClassConst $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        foreach ($node->consts as $const) {
            $constantName = (string) $const->name;
            if (strtoupper($constantName) === $constantName) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $constantName);
            return [$errorMessage];
        }

        return [];
    }
}
