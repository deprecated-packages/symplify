<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Type\CallableType;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeTraverser;
use Symplify\Astral\TypeAnalyzer\ClassMethodReturnTypeResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedCallableRule\NoMixedCallableRuleTest
 */
final class NoMixedCallableRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Make callable type explicit. Here is how: https://phpstan.org/writing-php-code/phpdoc-types#callables';

    public function __construct(
        private ClassMethodReturnTypeResolver $classMethodReturnTypeResolver,
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Variable::class, ClassMethod::class];
    }

    /**
     * @param Variable|ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node instanceof ClassMethod) {
            $elementType = $this->classMethodReturnTypeResolver->resolve($node, $scope);
        } else {
            $elementType = $scope->getType($node);
        }

        $ruleErrors = [];

        TypeTraverser::map($elementType, static function (Type $type, callable $callable) use (&$ruleErrors): Type {
            if (! $type instanceof CallableType) {
                return $callable($type, $callable);
            }

            // some params are defined, good
            if ($type->getParameters() !== []) {
                return $callable($type, $callable);
            }

            if (! $type->getReturnType() instanceof MixedType) {
                return $callable($type, $callable);
            }

            $ruleErrors[] = self::ERROR_MESSAGE;
            return $callable($type, $callable);
        });

        return $ruleErrors;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            self::ERROR_MESSAGE,
            [new CodeSample(
                <<<'CODE_SAMPLE'
function run(callable $callable)
{
    return $callable(100);
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
/**
 * @param callable(): int $callable
 */
function run(callable $callable): int
{
    return $callable(100);
}
CODE_SAMPLE
            )]
        );
    }
}
