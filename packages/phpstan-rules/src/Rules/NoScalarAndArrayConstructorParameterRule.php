<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Rules\Rule;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\Autodiscovery\Discovery;
use Symplify\Autodiscovery\Finder\AutodiscoveryFinder;
use Symplify\FlexLoader\Flex\FlexLoader;
use Symplify\PHPStanRules\Types\ScalarTypeAnalyser;
use Symplify\PHPStanRules\VariableAsParamAnalyser;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Throwable;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoScalarAndArrayConstructorParameterRule\NoScalarAndArrayConstructorParameterRuleTest
 */
final class NoScalarAndArrayConstructorParameterRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use scalar or array as constructor parameter. Use ParameterProvider service instead';

    /**
     * @var string
     * @see https://regex101.com/r/HDOhtp/4
     */
    private const VALUE_OBJECT_REGEX = '#\bValueObject|Entity|Event\b#';

    /**
     * @var string[]
     */
    private const ALLOWED_TYPES = [
        Rule::class,
        Throwable::class,
        // part of before construction of dependency injeciton
        Kernel::class,
        CompilerPassInterface::class,
        FlexLoader::class,
        AutodiscoveryFinder::class,
        Discovery::class,
        AutodiscoveryFinder::class,
        Extension::class,
        Application::class,
    ];

    /**
     * @var VariableAsParamAnalyser
     */
    private $variableAsParamAnalyser;

    /**
     * @var ScalarTypeAnalyser
     */
    private $scalarTypeAnalyser;

    public function __construct(
        VariableAsParamAnalyser $variableAsParamAnalyser,
        ScalarTypeAnalyser $scalarTypeAnalyser
    ) {
        $this->variableAsParamAnalyser = $variableAsParamAnalyser;
        $this->scalarTypeAnalyser = $scalarTypeAnalyser;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Variable::class];
    }

    /**
     * @param Variable $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->isClassAllowed($scope)) {
            return [];
        }

        if ($this->isValueObjectNamespace($scope)) {
            return [];
        }

        $functionReflection = $scope->getFunction();
        if (! $functionReflection instanceof MethodReflection) {
            return [];
        }

        if (! $this->variableAsParamAnalyser->isVariableFromConstructorParam($functionReflection, $node)) {
            return [];
        }

        // is variable in parameter?
        $variableType = $scope->getType($node);
        if (! $this->scalarTypeAnalyser->isScalarOrArrayType($variableType)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    /**
     * @var string
     */
    private $outputDirectory;

    public function __construct(string $outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    /**
     * @var string
     */
    private $outputDirectory;

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->outputDirectory = $parameterProvider->getStringParam(...);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isValueObjectNamespace(Scope $scope): bool
    {
        return (bool) Strings::match($scope->getFile(), self::VALUE_OBJECT_REGEX);
    }

    private function isClassAllowed(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return false;
        }

        foreach (self::ALLOWED_TYPES as $allowedType) {
            if (! is_a($classReflection->getName(), $allowedType, true)) {
                continue;
            }

            return true;
        }

        return false;
    }
}
