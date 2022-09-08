<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\MonorepoBuilder\Contract\Git\TagResolverInterface;
use Symplify\MonorepoBuilder\Git\MostRecentTagForCurrentBranchResolver;
use Symplify\MonorepoBuilder\Git\MostRecentTagResolver;
use Symplify\MonorepoBuilder\ValueObject\Option;

final class RecentTagResolverScopeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $isTagResolverScopeLimitedToCurrentBranch = $container->getParameter(Option::LIMIT_RECENT_TAG_RESOLVING_SCOPE_TO_CURRENT_BRANCH);

        if (! $isTagResolverScopeLimitedToCurrentBranch) {
            $container->setAlias(TagResolverInterface::class, MostRecentTagResolver::class)->setPublic(true);
            return;
        }

        $container->setAlias(TagResolverInterface::class, MostRecentTagForCurrentBranchResolver::class)->setPublic(true);
    }
}
