<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpMethodReflection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Extension\ConfigurationExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Naming\MissMatchingParamResolver;
use Symplify\PHPStanRules\ParentGuard\ParentElementResolver\ParentMethodResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\PHPStanRules\ValueObject\MissMatchingParamName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\SameNamedParamFamilyRule\SameNamedParamFamilyRuleTest
 */
final class SameNamedParamFamilyRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Arguments names conflicts with parent class method "%s". This will break named arguments';

    public function __construct(
        private ParentMethodResolver $parentMethodResolver,
        private SimpleNameResolver $simpleNameResolver,
        private MissMatchingParamResolver $missMatchingParamResolver
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        // @see https://stitcher.io/blog/php-8-named-arguments#named-arguments-in-depth
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
interface SomeInterface
{
    public function run($value);
}

final class SomeClass implements SomeInterface
{
    public function run($differentValue)
    {
    }
}
CODE_SAMPLE
             ,
                <<<'CODE_SAMPLE'
interface SomeInterface
{
    public function run($value);
}

final class SomeClass implements SomeInterface
{
    public function run($value)
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return mixed[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($scope, $node)) {
            return [];
        }

        $phpMethodReflection = $this->parentMethodResolver->resolveFromClassMethod($scope, $node);
        if (! $phpMethodReflection instanceof PhpMethodReflection) {
            return [];
        }

        $currentParamNames = $this->resolveClassMethodParamNames($node);
        $parentParamNames = $this->resolveMethodReflectionParamNames($phpMethodReflection);

        $conflictingParamNames = $this->missMatchingParamResolver->resolve($currentParamNames, $parentParamNames);

        $conflictingParamNames = $this->filterOutContainerBuilderMissMatch(
            $conflictingParamNames,
            $phpMethodReflection
        );

        // everything is the same
        if ($conflictingParamNames === []) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @param MissMatchingParamName[] $missMatchingParamNames
     * @return MissMatchingParamName[]
     */
    private function filterOutContainerBuilderMissMatch(
        array $missMatchingParamNames,
        PhpMethodReflection $phpMethodReflection
    ): array {
        $filteredMissMatchingParamNames = [];

        foreach ($missMatchingParamNames as $conflictingParamName) {
            if ($conflictingParamName->getCurrentName() === 'containerBuilder' && $this->isContainerBuilderSymfonyMissMatch(
                $phpMethodReflection
            )) {
                continue;
            }

            $filteredMissMatchingParamNames[] = $conflictingParamName;
        }

        return $filteredMissMatchingParamNames;
    }

    /**
     * @return string[]
     */
    private function resolveMethodReflectionParamNames(PhpMethodReflection $phpMethodReflection): array
    {
        $paramNames = [];

        $firstVariant = $phpMethodReflection->getVariants()[0];
        foreach ($firstVariant->getParameters() as $parameterReflectionWithPhpDoc) {
            $paramNames[] = $parameterReflectionWithPhpDoc->getName();
        }

        return $paramNames;
    }

    /**
     * @return string[]
     */
    private function resolveClassMethodParamNames(ClassMethod $classMethod): array
    {
        $paramNames = [];
        foreach ($classMethod->params as $param) {
            /** @var string $paramName */
            $paramName = $this->simpleNameResolver->getName($param->var);
            $paramNames[] = $paramName;
        }

        return $paramNames;
    }

    private function shouldSkip(Scope $scope, ClassMethod $classMethod): bool
    {
        if ($classMethod->isMagic()) {
            return true;
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return true;
        }

        if ($classMethod->params === []) {
            return true;
        }

        // no parent interface, class nor trait
        return count($classReflection->getAncestors()) === 1;
    }

    private function isContainerBuilderSymfonyMissMatch(PhpMethodReflection $phpMethodReflection): bool
    {
        $parentClassReflection = $phpMethodReflection->getDeclaringClass();
        // the Container vs ContainerBuilder missmatch
        return in_array($parentClassReflection->getName(), [
            ExtensionInterface::class,
            ConfigurationExtensionInterface::class,
            Bundle::class,
            BundleInterface::class,
            Kernel::class,
            CompilerPassInterface::class,
        ], true);
    }
}
