<?php declare(strict_types=1);

namespace Symplify\Statie\Contract\Templating;

interface FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function provide(): array;
}
