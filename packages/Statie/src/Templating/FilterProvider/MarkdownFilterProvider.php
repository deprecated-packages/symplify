<?php declare(strict_types=1);

namespace Symplify\Statie\Templating\FilterProvider;

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
            // usage in Latte: {$content|markdown}
            'markdown' => function (string $content): string {
                return $this->parsedownExtra->parse($content);
            },
        ];
    }
}
