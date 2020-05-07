<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoNullableParameterRule\NoNullableParameterRuleTest
 */
final class NoNullableParameterRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Parameter "%s" cannot be nullable';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errorMessages = [];
        foreach ($node->params as $param) {
            if ($param->type === null) {
                continue;
            }

            if (! $param->type instanceof NullableType) {
                continue;
            }

            $paramName = (string) $param->var->name;
            $errorMessages[] = sprintf(self::ERROR_MESSAGE, $paramName);
        }

        return $errorMessages;
    }
}
