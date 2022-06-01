<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Enum;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Reflection\Php\PhpParameterReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\StringType;
use Rector\Config\RectorConfig;
use Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator;
use Symplify\EasyCodingStandard\Config\ECSConfig;
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
        RectorConfig::class,
        ECSConfig::class,
        ParametersConfigurator::class,
        \Symplify\Astral\Naming\SimpleNameResolver::class,
        // set get option
        \Symfony\Component\Console\Input\InputInterface::class,
        \Symfony\Component\Console\Command\Command::class,
        // attributes
        \PhpParser\Node::class,
        \PHPStan\PhpDocParser\Ast\Node::class,
        \PHPStan\Type\Type::class,
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

            if (! $parameterReflection instanceof PhpParameterReflection) {
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
    private function resolveParameterReflections(MethodCall $methodCall, Scope $scope): mixed
    {
        $phpMethodReflection = $this->methodCallNodeAnalyzer->resolveMethodCallReflection($methodCall, $scope);
        if (! $phpMethodReflection instanceof PhpMethodReflection) {
            return [];
        }

        // is skipped type?
        $declaringClassReflection = $phpMethodReflection->getDeclaringClass();
        foreach (self::SKIPPED_CLASS_TYPES as $skippedClassType) {
            if ($declaringClassReflection->isSubclassOf($skippedClassType)) {
                return [];
            }

            if ($skippedClassType === $declaringClassReflection->getName()) {
                return [];
            }
        }

        $parametersAcceptor = ParametersAcceptorSelector::selectSingle($phpMethodReflection->getVariants());
        return $parametersAcceptor->getParameters();
    }
}
