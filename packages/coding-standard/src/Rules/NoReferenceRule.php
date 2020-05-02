<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoReferenceRule\NoReferenceRuleTest
 */
final class NoReferenceRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit return value over magic "&%s" reference';

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

        foreach ((array) $node->params as $param) {
            /** @var Param $param */
            if ($param->byRef) {
                $variableName = (string) $param->var->name;
                $errorMessages[] = sprintf(self::ERROR_MESSAGE, $variableName);
            }
        }

        return $errorMessages;
    }
}
