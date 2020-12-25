<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
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

            if (! $this->isNullableParam($param)) {
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
use PhpParser\Node;

class SomeClass
{
    public function run(?Node $node = null): void
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PhpParser\Node;

class SomeClass
{
    public function run(Node $node): void
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

    /**
     * @param Identifier|Name|NullableType|UnionType $typeNode
     */
    private function isForbiddenType(Node $typeNode): bool
    {
        if ($typeNode instanceof UnionType) {
            // not supported type
            return false;
        }

        $typeName = null;
        if ($typeNode instanceof NullableType) {
            $typeName = $this->simpleNameResolver->getName($typeNode->type);
        } else {
            $typeName = $this->simpleNameResolver->getName($typeNode);
        }

        if ($typeName === null) {
            return false;
        }

        return $this->isAMatch($typeName, $this->forbidddenTypes);
    }

    private function isNullableParam(Param $param): bool
    {
        if ($param->type instanceof NullableType) {
            return true;
        }

        if ($param->default === null) {
            return false;
        }

        if (! $param->default instanceof ConstFetch) {
            return false;
        }

        return $param->default->name->toLowerString() === 'null';
    }

    /**
     * @param string[] $forbidddenTypes
     */
    private function isAMatch(string $desiredType, array $forbidddenTypes): bool
    {
        foreach ($forbidddenTypes as $forbidddenType) {
            if (is_a($desiredType, $forbidddenType, true)) {
                return true;
            }
        }

        return false;
    }
}
