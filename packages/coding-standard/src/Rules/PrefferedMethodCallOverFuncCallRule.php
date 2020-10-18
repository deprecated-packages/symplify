<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\PhpParser\NodeNameResolver;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\PrefferedMethodCallOverFuncCallRule\PrefferedMethodCallOverFuncCallRuleTest
 */
final class PrefferedMethodCallOverFuncCallRule extends AbstractPrefferedCallOverFuncRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "%s->%s()" method call over "%s()" func call';

    /**
     * @var array<string, string[]>
     */
    private $funcCallToPrefferedMethodCalls = [];

    /**
     * @param array<string, string[]> $funcCallToPrefferedMethodCalls
     */
    public function __construct(NodeNameResolver $nodeNameResolver, array $funcCallToPrefferedMethodCalls = [])
    {
        parent::__construct($nodeNameResolver);

        $this->funcCallToPrefferedMethodCalls = $funcCallToPrefferedMethodCalls;
    }

    /**
     * @param FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $errorMessageParameters = $this->getErrorMessageParameters(
            $node,
            $scope,
            $this->funcCallToPrefferedMethodCalls
        );
        if ($errorMessageParameters === []) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, ...$errorMessageParameters);
        return [$errorMessage];
    }
}
