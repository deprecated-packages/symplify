<?php declare(strict_types=1);

namespace Symplify\Statie\Contract\Templating;

interface FilterProvidersAwareInterface
{
    public function addFilterProvider(FilterProviderInterface $filterProvider): void;
}
