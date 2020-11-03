<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\PhpParser\NodeNameResolver;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PrefferedStaticCallOverFuncCallRule\PrefferedStaticCallOverFuncCallRuleTest
 */
final class PrefferedStaticCallOverFuncCallRule extends AbstractPrefferedCallOverFuncRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "%s::%s()" static call over "%s()" func call';

    /**
     * @var array<string, string[]>
     */
    private $funcCallToPrefferedStaticCalls = [];

    /**
     * @param array<string, string[]> $funcCallToPrefferedStaticCalls
     */
    public function __construct(NodeNameResolver $nodeNameResolver, array $funcCallToPrefferedStaticCalls = [])
    {
        parent::__construct($nodeNameResolver);

        $this->funcCallToPrefferedStaticCalls = $funcCallToPrefferedStaticCalls;
    }

    /**
     * @param FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        foreach ($this->funcCallToPrefferedStaticCalls as $functionName => $staticCall) {
            if (! $this->isFuncCallToCallMatch($node, $scope, $functionName, $staticCall)) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $staticCall[0], $staticCall[1], $functionName);
            return [$errorMessage];
        }

        return [];
    }
}
