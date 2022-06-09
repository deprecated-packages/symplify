<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Enum;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeAnalyzer\MethodCall\MethodCallClassConstFetchPositionResolver;
use Symplify\PHPStanRules\Reflection\MethodCallNodeAnalyzer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\RequireEnumDocBlockOnConstantListPassRuleTest
 *
 * @implements Rule<MethodCall>
 */
final class RequireEnumDocBlockOnConstantListPassRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'On passing a constant, the method should have an enum type. See https://phpstan.org/writing-php-code/phpdoc-types#literals-and-constants';

    /**
     * These types expect constant lists that are not enums
     *
     * @var string[]
     */
    private const SKIPPED_CLASS_TYPES = [
        'Symplify\PackageBuilder\Parameter\ParameterProvider',
        AbstractConfigurator::class,
        SimpleNameResolver::class,
        ParameterBagInterface::class,
    ];

    public function __construct(
        private MethodCallNodeAnalyzer $methodCallNodeAnalyzer,
        private MethodCallClassConstFetchPositionResolver $methodCallClassConstFetchPositionResolver,
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // has argument of constant class reference
        $classConstFetchArgPositions = $this->methodCallClassConstFetchPositionResolver->resolve($node);
        if ($classConstFetchArgPositions === []) {
            return [];
        }

        $parameterReflections = $this->resolveParameterReflections($node, $scope);

        foreach ($parameterReflections as $position => $parameterReflection) {
            // is desired arg position?
            if (! in_array($position, $classConstFetchArgPositions, true)) {
                continue;
            }

            // this must be some more strict type
            if ($parameterReflection->getType() instanceof StringType) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class Direction
{
    public const LEFT = 'left';

    public const RIGHT = 'right';
}

final class Driver
{
    public function goToWork()
    {
        $this->turn(Direction::LEFT);
    }

    private function turn(string $direction)
    {
        // ...
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class Direction
{
    public const LEFT = 'left';

    public const RIGHT = 'right';
}

final class Driver
{
    public function goToWork()
    {
        $this->turn(Direction::LEFT);
    }

    /**
     * @param Direction::*
     */
    private function turn(string $direction)
    {
        // ...
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<int, ParameterReflection>
     */
    private function resolveParameterReflections(MethodCall $methodCall, Scope $scope): array
    {
        $phpMethodReflection = $this->methodCallNodeAnalyzer->resolveMethodCallReflection($methodCall, $scope);
        if (! $phpMethodReflection instanceof PhpMethodReflection) {
            return [];
        }

        // is skipped type?
        if ($this->shouldSkipClass($phpMethodReflection->getDeclaringClass())) {
            return [];
        }

        $parametersAcceptorWithPhpDocs = ParametersAcceptorSelector::selectSingle($phpMethodReflection->getVariants());
        return $parametersAcceptorWithPhpDocs->getParameters();
    }

    private function shouldSkipClass(ClassReflection $classReflection): bool
    {
        if ($classReflection->isInternal()) {
            return true;
        }

        $filename = $classReflection->getFileName();
        if (! is_string($filename)) {
            return true;
        }

        // skip vendor classes, as we cannot change them
        if (str_contains($filename, '/vendor/')) {
            return true;
        }

        foreach (self::SKIPPED_CLASS_TYPES as $skippedClassType) {
            // skip vendor class, as we can't reach it
            if ($classReflection->isSubclassOf($skippedClassType)) {
                return true;
            }

            if ($skippedClassType === $classReflection->getName()) {
                return true;
            }
        }

        return false;
    }
}
