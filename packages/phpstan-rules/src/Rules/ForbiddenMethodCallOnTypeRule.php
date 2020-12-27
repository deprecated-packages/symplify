<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\TypeWithClassName;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallOnTypeRule\ForbiddenMethodCallOnTypeRuleTest
 */
final class ForbiddenMethodCallOnTypeRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"%s()" call on "%s" type is not allowed';

    /**
     * @var array<string, string[]>
     */
    private $forbiddenMethodNamesByTypes = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param array<string, string[]> $forbiddenMethodNamesByTypes
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $forbiddenMethodNamesByTypes = [])
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->forbiddenMethodNamesByTypes = $forbiddenMethodNamesByTypes;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Prevent using certain method calls on certains types', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function process(SpecificType $specificType)
    {
        $specificType->nope();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function process(SpecificType $specificType)
    {
        $specificType->yes();
    }
}
CODE_SAMPLE
                ,
                [
                    'forbiddenMethodNamesByTypes' => [
                        'SpecificType' => ['nope'],
                    ],
                ],
            ),
        ]);
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
        foreach ($this->forbiddenMethodNamesByTypes as $type => $methodsNames) {
            if (! $this->simpleNameResolver->isNames($node->name, $methodsNames)) {
                continue;
            }

            if (! $this->isType($scope, $node->var, $type)) {
                continue;
            }

            $methodName = $this->simpleNameResolver->getName($node->name);
            $errorMessage = sprintf(self::ERROR_MESSAGE, $methodName, $type);

            return [$errorMessage];
        }

        return [];
    }

    private function isType(Scope $scope, Expr $expr, string $desiredType): bool
    {
        $callerType = $scope->getType($expr);
        if (! $callerType instanceof TypeWithClassName) {
            return false;
        }

        return is_a($callerType->getClassName(), $desiredType, true);
    }
}
