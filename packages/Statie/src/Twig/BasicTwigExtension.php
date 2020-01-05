<?php

declare(strict_types=1);

namespace Symplify\Statie\Twig;

use Iterator;
use Nette\Utils\DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class BasicTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): Iterator
    {
        yield new TwigFilter('md5', function ($value): string {
            return md5($value);
        });

        // xml
        // https://www.rubydoc.info/github/mojombo/jekyll/Jekyll%2FFilters:date_to_xmlschema
        // https://stackoverflow.com/a/26094939/1348344
        yield new TwigFilter('date_to_xmlschema', function ($value): string {
            return DateTime::from($value)->format('c');
        });

        // https://www.rubydoc.info/github/mojombo/jekyll/Jekyll%2FFilters:xml_escape
        // https://3v4l.org/Mng53
        yield new TwigFilter('xml_escape', function ($value): string {
            return htmlspecialchars($value);
        });
    }
}
