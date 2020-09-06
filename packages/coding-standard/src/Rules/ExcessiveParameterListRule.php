<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ExcessiveParameterListRule\ExcessiveParameterListRuleTest
 */
final class ExcessiveParameterListRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method "%s()" is using too many parameters - %d. Make it under %d';

    /**
     * @var int
     */
    private $maxParameterCount;

    public function __construct(int $maxParameterCount = 10)
    {
        $this->maxParameterCount = $maxParameterCount;
    }

    public function getNodeType(): string
    {
        return FunctionLike::class;
    }

    /**
     * @param FunctionLike $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $currentParameterCount = count((array) $node->getParams());
        if ($currentParameterCount <= $this->maxParameterCount) {
            return [];
        }

        $name = $this->resolveName($node);
        $message = sprintf(self::ERROR_MESSAGE, $name, $currentParameterCount, $this->maxParameterCount);
        return [$message];
    }

    private function resolveName(FunctionLike $functionLike): string
    {
        if ($functionLike instanceof ClassMethod || $functionLike instanceof Function_) {
            return (string) $functionLike->name;
        }

        if ($functionLike instanceof ArrowFunction) {
            return 'arrow function';
        }

        if ($functionLike instanceof Closure) {
            return 'closure';
        }

        return 'unknown';
    }
}
