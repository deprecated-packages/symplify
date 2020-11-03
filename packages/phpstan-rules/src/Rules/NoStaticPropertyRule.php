<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PHPStan\Analyser\Scope;
use PHPStan\DependencyInjection\Container;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\PHPStanRules\Types\ContainsTypeAnalyser;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoStaticPropertyRule\NoStaticPropertyRuleTest
 */
final class NoStaticPropertyRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use static property';

    /**
     * @var string[]
     */
    private const CACHEABLE_TYPES = [ContainerInterface::class, Container::class, KernelInterface::class];

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
        return [StaticPropertyFetch::class];
    }

    /**
     * @param StaticPropertyFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->containsTypeAnalyser->containsExprTypes($node, $scope, self::CACHEABLE_TYPES)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
