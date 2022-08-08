<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\UnionType;
use Symplify\PHPStanRules\TypeAnalyzer\ArrayShapeDetector;
use Symplify\PHPStanRules\TypeResolver\ClassMethodReturnTypeResolver;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingArrayShapeReturnArrayRule\NoMissingArrayShapeReturnArrayRuleTest
 * @implements Rule<ClassMethod>
 */
final class NoMissingArrayShapeReturnArrayRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Complete known array shape to the method @return type';

    public function __construct(
        private NodeFinder $nodeFinder,
        private ClassMethodReturnTypeResolver $classMethodReturnTypeResolver,
        private ArrayShapeDetector $arrayShapeDetector
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errorMessages = [];

        /** @var Return_[] $returns */
        $returns = $this->nodeFinder->findInstanceOf($node, Return_::class);

        foreach ($returns as $return) {
            if (! $return->expr instanceof Expr) {
                continue;
            }

            if (! $this->arrayShapeDetector->isTypeArrayShapeCandidate($return->expr, $scope)) {
                continue;
            }

            // skip event subscriber static method
            if ($node->name->toString() === 'getSubscribedEvents') {
                return [];
            }

            if ($this->hasClassMethodReturnConstantArrayType($node, $scope)) {
                continue;
            }

            $errorMessages[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->line($return->getLine())
                ->build();
        }

        return $errorMessages;
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
