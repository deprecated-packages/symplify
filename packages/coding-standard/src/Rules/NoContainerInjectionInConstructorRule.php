<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\CodingStandard\PHPStan\Types\ContainsTypeAnalyser;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoContainerInjectionInConstructorRule\NoContainerInjectionInConstructorRuleTest
 */
final class NoContainerInjectionInConstructorRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of container injection, use specific service';

    /**
     * @var ContainsTypeAnalyser
     */
    private $containsTypeAnalyser;

    public function __construct(ContainsTypeAnalyser $containsTypeAnalyser)
    {
        $this->containsTypeAnalyser = $containsTypeAnalyser;
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
        if (! $this->isInConstructMethod($scope)) {
            return [];
        }

        if (! $this->containsTypeAnalyser->containsExprTypes($node, $scope, [ContainerInterface::class])) {
            return [];
        }

        if ($this->containsTypeAnalyser->containsExprTypes($node, $scope, [ContainerBuilder::class])) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isInConstructMethod(Scope $scope): bool
    {
        $reflectionFunction = $scope->getFunction();
        if (! $reflectionFunction instanceof MethodReflection) {
            return false;
        }

        return $reflectionFunction->getName() === '__construct';
    }
}
