<?php declare(strict_types=1);

namespace Symplify\Statie\Twig;

use Iterator;
use Nette\Utils\Strings;
use ParsedownExtra;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class MarkdownTwigExtension extends AbstractExtension
{
    /**
     * @var ParsedownExtra
     */
    private $parsedownExtra;

    public function __construct(ParsedownExtra $parsedownExtra)
    {
        $this->parsedownExtra = $parsedownExtra;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): Iterator
    {
        // usage in Twig: {{ content|markdown }}
        yield new TwigFilter('markdown', function (string $content): string {
            $content = $this->parsedownExtra->parse($content);

            // remove <p></p>, it adds extra unwanted spaces
            $match = Strings::match($content, '#<p>(?<content>.*?)<\/p>#sm');
            if (isset($match['content'])) {
                return $match['content'];
            }

            return $content;
        });
    }
}
