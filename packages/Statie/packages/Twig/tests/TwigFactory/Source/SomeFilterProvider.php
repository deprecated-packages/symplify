<?php declare(strict_types=1);

namespace Symplify\Statie\Twig\Tests\TwigFactory\Source;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;

final class SomeFilterProvider implements FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            'someFilter' => function (string $content) {
                return strtoupper($content);
            },
        ];
    }
}
