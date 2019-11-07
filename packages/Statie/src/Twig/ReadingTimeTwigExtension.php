<?php declare(strict_types=1);

namespace Symplify\Statie\Twig;

use Iterator;
use Symplify\Statie\Templating\ReadingTimeResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @inspiration https://github.com/victorhaggqvist/Twig-sort-by-field
 */
final class ReadingTimeTwigExtension extends AbstractExtension
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
     * @return TwigFilter[]
     */
    public function getFilters(): Iterator
    {
        // usage in Twig: {{ content|reading_time }} mins → 4 mins
        yield new TwigFilter('reading_time', function (string $content): int {
            return $this->readingTimeResolver->resolveFromContent($content);
        });
    }
}
