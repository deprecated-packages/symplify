<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\ThisType;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PHPStanRules\TypeAnalyzer\ContainsTypeAnalyser;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\ForbiddenThisArgumentRuleTest
 */
final class ForbiddenThisArgumentRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '$this as argument is not allowed. Refactor method to service composition';

    /**
     * @var class-string[]
     */
    private const ALLOWED_CALLER_CLASSES = [
        // workaround type
        'Symplify\PackageBuilder\Reflection\PrivatesCaller',
    ];

    public function __construct(
        private ContainsTypeAnalyser $containsTypeAnalyser
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return CallLike::class;
    }

    /**
     * @param CallLike $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node instanceof MethodCall && ! $node instanceof FuncCall && ! $node instanceof StaticCall) {
            return [];
        }

        if ($this->shouldSkip($node, $scope)) {
            return [];
        }

        foreach ($node->getArgs() as $arg) {
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
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return $classReflection->isSubclassOf(Kernel::class);
    }

    private function shouldSkip(MethodCall | FuncCall | StaticCall $node, Scope $scope): bool
    {
        if ($node instanceof MethodCall) {
            return $this->containsTypeAnalyser->containsExprTypes($node->var, $scope, self::ALLOWED_CALLER_CLASSES);
        }

        if ($node instanceof FuncCall) {
            if (! $node->name instanceof Name) {
                return false;
            }

            return $node->name->toString() === 'method_exists';
        }

        return false;
    }
}
