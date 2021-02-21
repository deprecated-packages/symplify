<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\NullableType;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Php\TypeChecker;
use Symplify\PHPStanRules\ParentGuard\ParentClassMethodGuard;
use Symplify\PHPStanRules\TypeResolver\NullableTypeResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableReturnRule\ForbiddenNullableReturnRuleTest
 */
final class ForbiddenNullableReturnRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Return type "%s" cannot be nullable';

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
     * @var ParentClassMethodGuard
     */
    private $parentClassMethodGuard;

    /**
     * @param class-string[] $forbiddenTypes
     * @param class-string[] $allowedTypes
     */
    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        TypeChecker $typeChecker,
        ParentClassMethodGuard $parentClassMethodGuard,
        array $forbiddenTypes = [],
        array $allowedTypes = []
    ) {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->typeChecker = $typeChecker;
        $this->forbiddenTypes = $forbiddenTypes;
        $this->allowedTypes = $allowedTypes;
        $this->parentClassMethodGuard = $parentClassMethodGuard;
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
        if ($this->parentClassMethodGuard->isFunctionLikeProtected($node, $scope)) {
            return [];
        }


        $returnType = $node->returnType;
        if (! $returnType instanceof NullableType) {
            return [];
        }

        $mainReturnType = $this->simpleNameResolver->getName($returnType->type);
        if (! $this->isForbiddenType($mainReturnType)) {
            return [];
        }

        if ($this->isAllowedType($mainReturnType)) {
            return [];
        }

        $paramName = $this->simpleNameResolver->getName($mainReturnType);
        $errorMessage = sprintf(self::ERROR_MESSAGE, $paramName);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
use PhpParser\Node;

class SomeClass
{
    public function run(): ?Node
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PhpParser\Node;

class SomeClass
{
    public function run(): Node
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
        if ($this->forbiddenTypes === []) {
            return true;
        }

        return $this->typeChecker->isInstanceOf($typeName, $this->forbiddenTypes);
    }

    private function isAllowedType(string $typeName): bool
    {
        return $this->typeChecker->isInstanceOf($typeName, $this->allowedTypes);
    }
}
