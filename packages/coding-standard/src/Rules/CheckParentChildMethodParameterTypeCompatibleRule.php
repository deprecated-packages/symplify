<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\PHPStan\ParentMethodAnalyser;
use Symplify\CodingStandard\PHPStan\ParentClassMethodNodeResolver;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\CheckParentChildMethodParameterTypeCompatibleRuleTest
 */
final class CheckParentChildMethodParameterTypeCompatibleRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Parent and Child Method Parameter must be compatible';

    /**
     * @var ParentMethodAnalyser
     */
    private $parentMethodAnalyser;

    /** @var ParentClassMethodNodeResolver */
    private $parentClassMethodNodeResolver;

    public function __construct(ParentMethodAnalyser $parentMethodAnalyser, ParentClassMethodNodeResolver $parentClassMethodNodeResolver)
    {
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

        // not inside class or no extends → skip
        if ($class === null || $class->extends === null) {
            return [];
        }

        // no parameter → skip
        if ($node->params === []) {
            return [];
        }

        // not has parent method? → skip
        $methodName = (string) $node->name;
        if (! $this->parentMethodAnalyser->hasParentClassMethodWithSameName($scope, $methodName)) {
            return [];
        }

        $parentParameters = $this->parentClassMethodNodeResolver->resolveParentClassMethodParams($scope, $methodName);
        $parentParameterTypes = [];

        foreach ($parentParameters as $param) {
            $parentParameterTypes[] = $param->type->toString();
        }

        $currentParameterTypes = [];
        foreach ($node->params as $param) {
            $currentParameterTypes[] = $param->type->toString();
        }

        if ($parentParameterTypes === $currentParameterTypes) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
