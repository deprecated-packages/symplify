<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\UnionType;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\Astral\TypeAnalyzer\ClassMethodReturnTypeResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingArrayShapeReturnArrayRule\NoMissingArrayShapeReturnArrayRuleTest
 */
final class NoMissingArrayShapeReturnArrayRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Complete known array shape to the method @return type';

    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder,
        private ClassMethodReturnTypeResolver $classMethodReturnTypeResolver,
        private \Symplify\PHPStanRules\TypeAnalyzer\ArrayShapeDetector $arrayShapeDetector
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Return_::class];
    }

    /**
     * @param Return_ $node
     * @return mixed[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->expr instanceof Expr) {
            return [];
        }

        if (! $this->arrayShapeDetector->isTypeArrayShapeCandidate($node->expr, $scope)) {
            return [];
        }

        $parentClassMethod = $this->simpleNodeFinder->findFirstParentByType($node, ClassMethod::class);
        if (! $parentClassMethod instanceof ClassMethod) {
            return [];
        }

        // skip event subscriber static method
        $methodName = $parentClassMethod->name->toString();
        if ($methodName === 'getSubscribedEvents') {
            return [];
        }

        if ($this->hasClassMethodReturnConstantArrayType($parentClassMethod, $scope)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
function run(string $name)
{
    return ['name' => $name];
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
/**
 * @return array{name: string}
 */
function run(string $name)
{
    return ['name' => $name];
}
CODE_SAMPLE
            ),
        ]);
    }

    private function hasClassMethodReturnConstantArrayType(ClassMethod $classMethod, Scope $scope): bool
    {
        $classMethodReturnType = $this->classMethodReturnTypeResolver->resolve($classMethod, $scope);
        if ($classMethodReturnType instanceof ConstantArrayType) {
            return true;
        }

        if ($classMethodReturnType instanceof UnionType) {
            foreach ($classMethodReturnType->getTypes() as $unionedType) {
                if ($unionedType instanceof ConstantArrayType) {
                    return true;
                }
            }
        }

        return false;
    }
}
