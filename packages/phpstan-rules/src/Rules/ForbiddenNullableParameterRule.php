<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule\ForbiddenNullableParameterRuleTest
 */
final class ForbiddenNullableParameterRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Parameter "%s" cannot be nullable';

    /**
     * @var string[]
     */
    private $forbidddenTypes = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param string[] $forbidddenTypes
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $forbidddenTypes)
    {
        $this->forbidddenTypes = $forbidddenTypes;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $errorMessages = [];
        foreach ($node->params as $param) {
            if ($param->type === null) {
                continue;
            }

            if (! $param->type instanceof NullableType) {
                continue;
            }

            $nullableType = $param->type;
            if (! $this->isForbiddenType($nullableType)) {
                continue;
            }

            $paramName = (string) $param->var->name;
            $errorMessages[] = sprintf(self::ERROR_MESSAGE, $paramName);
        }

        return $errorMessages;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run(?\PhpParser\Node $node = null): void
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run(\PhpParser\Node $node): void
    {
    }
}
CODE_SAMPLE
                ,
                [
                    'forbidddenTypes' => [Node::class],
                ]
            ),
        ]);
    }

    private function isForbiddenType(NullableType $nullableType): bool
    {
        $nullableTypeName = $this->simpleNameResolver->getName($nullableType->type);
        if ($nullableTypeName === null) {
            return false;
        }

        foreach ($this->forbidddenTypes as $forbidddenType) {
            if (is_a($nullableTypeName, $forbidddenType, true)) {
                return true;
            }
        }

        return false;
    }
}
