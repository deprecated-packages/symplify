<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

<<<<<<< HEAD
use Symfony\Component\Finder\Finder;
=======
>>>>>>> [PHPStanRules] Enable the no assign fluent
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeTraverser;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\MixedType;
<<<<<<< HEAD
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\VoidType;
=======
use PHPStan\Type\NeverType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\VoidType;
use Symfony\Component\Finder\Finder;
>>>>>>> [PHPStanRules] Enable the no assign fluent
use Symplify\PHPStanRules\NodeAnalyzer\MethodCall\AllowedChainCallSkipper;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\NoMissingAssingNoVoidMethodCallRuleTest
 *
 * @implements Rule<Expression>
 */
final class NoMissingAssingNoVoidMethodCallRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method call return value that should be used, but is not';

    /**
     * @var string[]
     */
    private const SKIPPED_TYPES = [
        NodeTraverser::class,
<<<<<<< HEAD
        Finder::class,
        'Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator',
=======
        \Nette\Neon\Traverser::class,
        Finder::class,
        'Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator',
        'PhpCsFixer\Tokenizer\Tokens',
        \PHP_CodeSniffer\Fixer::class,
>>>>>>> [PHPStanRules] Enable the no assign fluent
    ];

    public function __construct(
        private AllowedChainCallSkipper $allowedChainCallSkipper,
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Expression::class;
    }

    /**
     * @param Expression $node
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->expr instanceof MethodCall) {
            return [];
        }

        $methodCall = $node->expr;
        $methodCallReturnType = $scope->getType($methodCall);

        if ($methodCallReturnType instanceof VoidType) {
            return [];
        }

<<<<<<< HEAD
=======
        if ($methodCallReturnType instanceof NeverType) {
            return [];
        }

>>>>>>> [PHPStanRules] Enable the no assign fluent
        if ($this->isFluentMethodCall($methodCallReturnType, $scope, $methodCall)) {
            return [];
        }

        if ($methodCallReturnType instanceof MixedType) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        $this->getResult();
    }

    private function getResult()
    {
        return [];
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        return $this->getResult();
    }

    private function getResult()
    {
        return [];
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isFluentMethodCall(Type $methodCallReturnType, Scope $scope, MethodCall $methodCall): bool
    {
        if ($this->allowedChainCallSkipper->isAllowedFluentMethodCall($scope, $methodCall, self::SKIPPED_TYPES)) {
            return true;
        }

<<<<<<< HEAD
        // 2. filter skipped call return types
        if ($methodCallReturnType instanceof ObjectType) {

=======
        if ($methodCallReturnType instanceof ObjectType) {
>>>>>>> [PHPStanRules] Enable the no assign fluent
            // 3. skip self static call
            $currentClassReflection = $scope->getClassReflection();
            if ($currentClassReflection instanceof ClassReflection) {
                return $currentClassReflection->getName() === $methodCallReturnType->getClassName();
            }
        }

        return false;
    }
}
