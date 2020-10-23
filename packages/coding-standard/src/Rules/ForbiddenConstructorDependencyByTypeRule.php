<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;
use PhpParser\Node\Name;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenConstructorDependencyByTypeRule\ForbiddenConstructorDependencyByTypeRule
 */
final class ForbiddenConstructorDependencyByTypeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Object instance of %s is forbidden to be passed to constructor';

    /**
     * @var string[]
     */
    private $forbiddenTypes = [];

    /**
     * @param string[] $forbiddenTypes
     */
    public function __construct(array $forbiddenTypes = [])
    {
        $this->forbiddenTypes = $forbiddenTypes;
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
        if ($scope->getClassReflection() === null) {
            return [];
        }

        $methodName = (string) $node->name;
        if ($methodName !== '__construct') {
            return [];
        }

        $params = $node->params;
        if ($params === []) {
            return [];
        }

        foreach ($params as $param) {
            if (! $param->type instanceof Name) {
                continue;
            }

            $paramType = $param->type->toString();
            if (in_array($paramType, $this->forbiddenTypes, true)) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }
}
