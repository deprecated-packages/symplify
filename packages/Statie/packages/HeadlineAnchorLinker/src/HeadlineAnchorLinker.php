<?php declare(strict_types=1);

namespace Symplify\Statie\HeadlineAnchorLinker;

use Nette\Utils\Strings;

final class HeadlineAnchorLinker
{
    /**
     * @var string
     */
    private const HEADLINE_PATTERN = '#<h(?<level>[2-6])>(?<title>.*?)<\/h[2-6]>#';

    /**
     * @var string
     */
    private const LINK_PATTERN = '#<a.*>(.*)<\/a>#';

    /**
     * Before:
     * - <h2>Some headline</h2>
     *
     * After:
     * - <h2 id="some-headline"><a href="#some-headline" class="heading-anchor">Some headline</a></h2>
     */
    public function processContent(string $content): string
    {
        return Strings::replace($content, self::HEADLINE_PATTERN, function (array $result): string {
            $titleWithoutTags = strip_tags($result['title']);
            $headlineId = Strings::webalize($titleWithoutTags);
            $titleWithLink = Strings::match($result['title'], self::LINK_PATTERN);
            $titleHasLink = is_array($titleWithLink) ? count($titleWithLink) > 0 : false;

            // Title contains <a> element
            if ($result['title'] !== $titleWithoutTags && $titleHasLink) {
                return sprintf(
                    '<h%s id="%s">%s</h%s>',
                    $result['level'],
                    $headlineId,
                    $result['title'],
                    $result['level']
                );
            }

            return sprintf(
                '<h%s id="%s"><a href="#%s" class="heading-anchor">%s</a></h%s>',
                $result['level'],
                $headlineId,
                $headlineId,
                $result['title'],
                $result['level']
            );
        });
    }
}
