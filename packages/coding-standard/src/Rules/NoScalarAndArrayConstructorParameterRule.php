<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use Symplify\CodingStandard\PHPStan\Types\ScalarTypeAnalyser;
use Symplify\CodingStandard\PHPStan\VariableAsParamAnalyser;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoScalarAndArrayConstructorParameterRule\NoScalarAndArrayConstructorParameterRuleTest
 */
final class NoScalarAndArrayConstructorParameterRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use scalar or array as constructor parameter. Use ParameterProvider service instead';

    /**
     * @var string
     * @see https://regex101.com/r/HDOhtp/3
     */
    private const VALUE_OBJECT_REGEX = '#\bValueObject\b#';

    /**
     * @var VariableAsParamAnalyser
     */
    private $variableAsParamTypeAnalyser;

    /**
     * @var ScalarTypeAnalyser
     */
    private $scalarTypeAnalyser;

    public function __construct(
        VariableAsParamAnalyser $variableAsParamTypeAnalyser,
        ScalarTypeAnalyser $scalarTypeAnalyser
    ) {
        $this->variableAsParamTypeAnalyser = $variableAsParamTypeAnalyser;
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
        if ($this->isValueObjectNamespace($scope)) {
            return [];
        }

        $functionReflection = $scope->getFunction();
        if (! $functionReflection instanceof MethodReflection) {
            return [];
        }

        if (! $this->variableAsParamTypeAnalyser->isVariableFromConstructorParam($functionReflection, $node)) {
            return [];
        }

        // is variable in parameter?
        $variableType = $scope->getType($node);
        if (! $this->scalarTypeAnalyser->isScalarOrArrayType($variableType)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isValueObjectNamespace(Scope $scope): bool
    {
        return (bool) Strings::match($scope->getFile(), self::VALUE_OBJECT_REGEX);
    }
}
