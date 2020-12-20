<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\ParentClassMethodNodeResolver;
use Symplify\PHPStanRules\ParentMethodAnalyser;
use Symplify\PHPStanRules\ValueObject\MethodName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\CheckParentChildMethodParameterTypeCompatibleRuleTest
 */
final class CheckParentChildMethodParameterTypeCompatibleRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method parameters must be compatible with its parent';

    /**
     * @var ParentMethodAnalyser
     */
    private $parentMethodAnalyser;

    /**
     * @var ParentClassMethodNodeResolver
     */
    private $parentClassMethodNodeResolver;

    public function __construct(
        ParentMethodAnalyser $parentMethodAnalyser,
        ParentClassMethodNodeResolver $parentClassMethodNodeResolver
    ) {
        $this->parentMethodAnalyser = $parentMethodAnalyser;
        $this->parentClassMethodNodeResolver = $parentClassMethodNodeResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var Class_|null $class */
        $class = $this->resolveCurrentClass($node);

        if ($this->shouldSkipClass($class)) {
            return [];
        }

        // method name is __construct or not has parent method → skip
        $methodName = (string) $node->name;
        if ($methodName === MethodName::CONSTRUCTOR) {
            return [];
        }

        if (! $this->parentMethodAnalyser->hasParentClassMethodWithSameName($scope, $methodName)) {
            return [];
        }

        $parentParameters = $this->parentClassMethodNodeResolver->resolveParentClassMethodParams($scope, $methodName);
        $parentParameterTypes = $this->getParameterTypes($parentParameters);
        $currentParameterTypes = $this->getParameterTypes($node->params);

        if ($parentParameterTypes === $currentParameterTypes) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class ParentClass
{
    public function run(string $someParameter)
    {
    }
}

class SomeClass extends ParentClass
{
    public function run($someParameter)
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class ParentClass
{
    public function run(string $someParameter)
    {
    }
}

class SomeClass extends ParentClass
{
    public function run(string $someParameter)
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]|null[]
     */
    private function getParameterTypes(array $params): array
    {
        $parameterTypes = [];
        foreach ($params as $param) {
            $parameterTypes[] = $this->getParamType($param->type);
        }

        return $parameterTypes;
    }

    private function getParamType(?Node $node): ?string
    {
        if ($node instanceof Identifier) {
            return $node->name;
        }

        if ($node === null) {
            return null;
        }

        if ($node instanceof NullableType) {
            $node = $node->type;
            return $this->getParamType($node);
        }

        if (method_exists($node, 'toString')) {
            return $node->toString();
        }

        return null;
    }

    private function shouldSkipClass(?Class_ $class): bool
    {
        // not inside class → skip
        if ($class === null) {
            return true;
        }

        // no extends and no implements → skip
        if ($class->extends !== null) {
            return false;
        }

        $implements = (array) $class->implements;
        return $implements === [];
    }
}
