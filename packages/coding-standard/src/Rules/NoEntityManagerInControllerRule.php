<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoEntityManagerInControllerRule\NoEntityManagerInControllerRuleTest
 */
final class NoEntityManagerInControllerRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use specific repository over entity manager in Controller';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ((string) $node->name !== '__construct') {
            return [];
        }

        if (! $this->isInControllerClass($scope)) {
            return [];
        }

        foreach ($node->params as $param) {
            if (! $this->isEntityManagerParam($param)) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    private function isInControllerClass(Scope $scope): bool
    {
        if ($scope->getClassReflection() === null) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection->getParentClassesNames() === []) {
            return false;
        }

        return Strings::endsWith($classReflection->getName(), 'Controller');
    }

    private function isEntityManagerParam(Param $param): bool
    {
        if ($param->type === null) {
            return false;
        }

        if (! $param->type instanceof Name) {
            return false;
        }

        $paramType = $param->type->toString();
        if (is_a($paramType, 'Doctrine\ORM\EntityManager', true)) {
            return true;
        }

        return is_a($paramType, 'Doctrine\ORM\EntityManagerInterface', true);
    }
}
