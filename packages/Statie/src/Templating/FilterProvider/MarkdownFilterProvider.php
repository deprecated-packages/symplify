<?php declare(strict_types=1);

namespace Symplify\Statie\Templating\FilterProvider;

use Nette\Utils\Strings;
use ParsedownExtra;
use Symplify\Statie\Contract\Templating\FilterProviderInterface;

final class MarkdownFilterProvider implements FilterProviderInterface
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
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            // usage in Twig: {{ content|markdown }}
            'markdown' => function (string $content): string {
                $content = $this->parsedownExtra->parse($content);

                // remove <p></p>, it adds extra unwanted spaces
                $match = Strings::match($content, '#<p>(?<content>.*?)<\/p>#sm');
                if (isset($match['content'])) {
                    return $match['content'];
                }

                return $content;
            },
        ];
    }
}
