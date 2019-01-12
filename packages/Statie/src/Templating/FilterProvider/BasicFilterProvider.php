<?php declare(strict_types=1);

namespace Symplify\Statie\Templating\FilterProvider;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;

final class BasicFilterProvider implements FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            'md5' => function ($value) {
                return md5($value);
            },
        ];
    }
}
