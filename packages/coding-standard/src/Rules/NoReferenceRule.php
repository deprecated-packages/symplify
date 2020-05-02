<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\ClosureUse;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoReferenceRule\NoReferenceRuleTest
 */
final class NoReferenceRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit return value over magic &reference';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [
            ClassMethod::class,
            Function_::class,
            AssignRef::class,
            Arg::class,
            ClosureUse::class,
            Foreach_::class,
            ArrayItem::class,
            ArrowFunction::class,
            Closure::class,
            ClosureUse::class,
        ];
    }

    /**
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $errorMessages = [];

        if (property_exists($node, 'byRef')) {
            if ($node->byRef) {
                $errorMessages[] = self::ERROR_MESSAGE;
            }
        }

        if ($node instanceof AssignRef) {
            $errorMessages[] = self::ERROR_MESSAGE;
        }

        $paramErrorMessage = $this->collectParamErrorMessages($node);

        return array_merge($errorMessages, $paramErrorMessage);
    }

    /**
     * @return string[]
     */
    private function collectParamErrorMessages(Node $node): array
    {
        if (! $node instanceof Function_ && ! $node instanceof ClassMethod) {
            return [];
        }

        $errorMessages = [];
        foreach ((array) $node->params as $param) {
            /** @var Param $param */
            if (! $param->byRef) {
                continue;
            }

            $errorMessages[] = self::ERROR_MESSAGE;
        }

        return $errorMessages;
    }
}
