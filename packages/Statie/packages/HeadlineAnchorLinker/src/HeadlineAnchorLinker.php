<?php declare(strict_types=1);

namespace Symplify\Statie\HeadlineAnchorLinker;

use Nette\Utils\Strings;
use function Safe\sprintf;

final class HeadlineAnchorLinker
{
    /**
     * @var string
     */
    private const HEADLINE_PATTERN = '#<h(?<level>[1-6])>(?<title>.*?)<\/h[1-6]>#';

    /**
     * Before:
     * - <h1>Some headline</h1>
     *
     * After:
     * - <h1 id="some-headline"><a href="#some-headline">Some headline</a></h1>
     */
    public function processContent(string $content): string
    {
        return Strings::replace($content, self::HEADLINE_PATTERN, function (array $result): string {
            $headlineId = Strings::webalize($result['title']);

            return sprintf(
                '<h%s id="%s"><a href="#%s">%s</a></h%s>',
                $result['level'],
                $headlineId,
                $headlineId,
                $result['title'],
                $result['level']
            );
        });
    }
}
