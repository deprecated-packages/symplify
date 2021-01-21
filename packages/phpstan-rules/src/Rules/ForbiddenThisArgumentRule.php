<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ThisType;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Php\TypeChecker;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\PHPStanRules\Types\ObjectTypeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\ForbiddenThisArgumentRuleTest
 */
final class ForbiddenThisArgumentRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '$this as argument is not allowed. Refator method to service composition';

    /**
     * @var string[]
     */
    private const ALLOWED_PARENT_CLASSES = [Kernel::class];

    /**
     * @var string[]
     */
    private const ALLOWED_CALLER_CLASSES = [
        // workaround type
        PrivatesCaller::class,
    ];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var TypeChecker
     */
    private $typeChecker;

    /**
     * @var ObjectTypeAnalyzer
     */
    private $objectTypeAnalyzer;

    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        TypeChecker $typeChecker,
        ObjectTypeAnalyzer $objectTypeAnalyzer
    ) {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->typeChecker = $typeChecker;
        $this->objectTypeAnalyzer = $objectTypeAnalyzer;
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
        if ($this->skipCaller($node, $scope)) {
            return [];
        }

        foreach ($node->args as $arg) {
            $argType = $scope->getType($arg->value);
            if (! $argType instanceof ThisType) {
                continue;
            }

            if ($this->shouldSkipClass($scope)) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$this->someService->process($this, ...);
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$this->someService->process($value, ...);
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipClass(Scope $scope): bool
    {
        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return false;
        }

        return $this->typeChecker->isInstanceOf($className, self::ALLOWED_PARENT_CLASSES);
    }

    private function skipCaller(MethodCall $methodCall, Scope $scope): bool
    {
        $callerType = $scope->getType($methodCall->var);

        foreach (self::ALLOWED_CALLER_CLASSES as $allowedCallerClass) {
            if ($this->objectTypeAnalyzer->isObjectOrUnionOfObjectType($callerType, $allowedCallerClass)) {
                return true;
            }
        }

        return false;
    }
}
