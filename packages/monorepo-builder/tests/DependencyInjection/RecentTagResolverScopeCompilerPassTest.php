<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\MonorepoBuilder\Contract\Git\TagResolverInterface;
use Symplify\MonorepoBuilder\Git\MostRecentTagForCurrentBranchResolver;
use Symplify\MonorepoBuilder\Git\MostRecentTagResolver;
use Symplify\MonorepoBuilder\Kernel\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Exception\MissingServiceException;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class RecentTagResolverScopeCompilerPassTest extends AbstractKernelTestCase
{
    /**
     * @throws ShouldNotHappenException
     * @throws MissingServiceException
     */
    public function testReturnRecentTagResolverForCurrentBranchWhenLimitedScopeIsEnabled(): void
    {
        $this->bootKernelWithConfigs(
            MonorepoBuilderKernel::class,
            [
                __DIR__ . '/config/enabled_limited_recent_tag_resolving_scope.php',
            ]
        );

        self::assertInstanceOf(
            MostRecentTagForCurrentBranchResolver::class,
            $this->getService(TagResolverInterface::class)
        );
    }

    /**
     * @throws ShouldNotHappenException
     * @throws MissingServiceException
     */
    public function testReturnDefaultRecentTagResolverWhenLimitedScopeIsDisabled(): void
    {
        $this->bootKernelWithConfigs(
            MonorepoBuilderKernel::class,
            [
                __DIR__ . '/config/disabled_limited_recent_tag_resolving_scope.php',
            ]
        );

        self::assertInstanceOf(
            MostRecentTagResolver::class,
            $this->getService(TagResolverInterface::class)
        );
    }

    /**
     * @throws ShouldNotHappenException
     * @throws MissingServiceException
     */
    public function testReturnDefaultRecentTagResolverWhenLimitedScopeIsNotExplicitlyDefined(): void
    {
        $this->bootKernelWithConfigs(
            MonorepoBuilderKernel::class,
            [
                __DIR__ . '/config/not_explicitly_defined_limited_recent_tag_resolving_scope.php',
            ]
        );

        self::assertInstanceOf(
            MostRecentTagResolver::class,
            $this->getService(TagResolverInterface::class)
        );
    }
}
