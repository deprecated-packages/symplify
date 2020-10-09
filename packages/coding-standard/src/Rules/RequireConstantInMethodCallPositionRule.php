<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\PHPStan\Types\ContainsTypeAnalyser;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\RequireConstantInMethodCallPositionRule\RequireConstantInMethodCallPositionRuleTest
 */
final class RequireConstantInMethodCallPositionRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Parameter argument on position %d must use %s constant';

    /**
     * @var ContainsTypeAnalyser
     */
    private $containsTypeAnalyser;

    /**
     * @var array<class-string, mixed[]>
     */
    private $requiredLocalConstantInMethodCall = [];

    /**
     * @var array<class-string, mixed[]>
     */
    private $requiredExternalConstantInMethodCall = [];

    /**
     * @param array<class-string, mixed[]> $requiredLocalConstantInMethodCall
     * @param array<class-string, mixed[]> $requiredExternalConstantInMethodCall
     */
    public function __construct(
        ContainsTypeAnalyser $containsTypeAnalyser,
        array $requiredLocalConstantInMethodCall = [],
        array $requiredExternalConstantInMethodCall = []
    ) {
        $this->containsTypeAnalyser = $containsTypeAnalyser;
        $this->requiredLocalConstantInMethodCall = $requiredLocalConstantInMethodCall;
        $this->requiredExternalConstantInMethodCall = $requiredExternalConstantInMethodCall;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
    }
}
