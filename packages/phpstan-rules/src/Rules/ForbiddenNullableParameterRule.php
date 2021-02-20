<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Php\TypeChecker;
use Symplify\PHPStanRules\TypeResolver\NullableTypeResolver;
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
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var TypeChecker
     */
    private $typeChecker;

    /**
     * @var class-string[]
     */
    private $forbiddenTypes = [];

    /**
     * @var class-string[]
     */
    private $allowedTypes = [];

    /**
     * @var NullableTypeResolver
     */
    private $nullableTypeResolver;

    /**
     * @param class-string[] $forbiddenTypes
     * @param class-string[] $allowedTypes
     */
    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        TypeChecker $typeChecker,
        NullableTypeResolver $nullableTypeResolver,
        array $forbiddenTypes = [],
        array $allowedTypes = []
    ) {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->typeChecker = $typeChecker;
        $this->forbiddenTypes = $forbiddenTypes;
        $this->allowedTypes = $allowedTypes;
        $this->nullableTypeResolver = $nullableTypeResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Function_::class, Closure::class];
    }

    /**
     * @param ClassMethod|Function_|Closure $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $errorMessages = [];
        foreach ($node->params as $param) {
            $paramType = $param->type;
            if ($paramType === null) {
                continue;
            }

            if (! $this->isNullableParam($param)) {
                continue;
            }

            $normalType = $this->nullableTypeResolver->resolveNormalType($paramType);
            if ($normalType === null) {
                continue;
            }

            if (! $this->isForbiddenType($normalType)) {
                continue;
            }

            if ($this->isAllowedType($normalType)) {
                continue;
            }

            $paramName = $this->simpleNameResolver->getName($param->var);
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
                    'forbiddenTypes' => [Node::class],
                    'allowedTypes' => [String_::class],
                ]
            ),
        ]);
    }

    private function isForbiddenType(string $typeName): bool
    {
        return $this->typeChecker->isInstanceOf($typeName, $this->forbiddenTypes);
    }

    private function isAllowedType(string $typeName): bool
    {
        return $this->typeChecker->isInstanceOf($typeName, $this->allowedTypes);
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

        return $this->simpleNameResolver->isName($param->default, 'null');
    }
}
