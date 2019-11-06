<?php declare(strict_types=1);

namespace Symplify\Statie\Templating\FilterProvider;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\Templating\ReadingTimeResolver;

/**
 * @inspiration https://github.com/victorhaggqvist/Twig-sort-by-field
 */
final class ReadingTimeFilterProvider implements FilterProviderInterface
{
    /**
     * @var ReadingTimeResolver
     */
    private $readingTimeResolver;

    public function __construct(ReadingTimeResolver $readingTimeResolver)
    {
        $this->readingTimeResolver = $readingTimeResolver;
    }

    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            // usage in Twig: {{ content|reading_time }} mins → 4 mins
            'reading_time' => function (string $content): int {
                return $this->readingTimeResolver->resolveFromContent($content);
            },
        ];
    }
}
