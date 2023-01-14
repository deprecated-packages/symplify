<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\MixedType;
use PHPStan\Type\NeverType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
use PHPStan\Type\VoidType;
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
        'Dotenv\Dotenv',
        'Symfony\Component\Form\FormBuilderInterface',
        'Symfony\Component\OptionsResolver\Options',
        'Symfony\Component\Form\FormInterface',
        'Symfony\Component\Finder\Finder',
        'PhpParser\NodeTraverser',
        'Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator',
        'Symfony\Component\Routing\Loader\Configurator\ImportConfigurator',
        'Nette\Neon\Traverser',
        'PHP_CodeSniffer\Fixer',
        'PhpCsFixer\Tokenizer\Tokens',
        'Latte\Engine',
        'React\Promise\Promise',
        'React\Promise\PromiseInterface',
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

        if ($this->isVoidishType($methodCallReturnType)) {
            return [];
        }

        if ($this->isFluentMethodCall($methodCallReturnType, $scope, $methodCall)) {
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

        // 2. filter skipped call return types
        if ($methodCallReturnType instanceof ThisType) {
            $methodCallReturnType = $methodCallReturnType->getStaticObjectType();
        }

        $currentClassReflection = $scope->getClassReflection();

        if ($methodCallReturnType instanceof ObjectType) {
            // 3. skip self static call
            return $currentClassReflection instanceof ClassReflection && $currentClassReflection->getName() === $methodCallReturnType->getClassName();
        }

        $callerType = $scope->getType($methodCall->var);
        return $callerType->equals($methodCallReturnType);
    }

    private function isVoidishType(Type $type): bool
    {
        if ($type instanceof VoidType) {
            return true;
        }

        if ($type instanceof NeverType) {
            return true;
        }

        return $type instanceof MixedType;
    }
}
