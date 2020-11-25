<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\CaseConverter;

use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class SuffixCaseConverter implements CaseConverterInterface
{
    /**
     * @see https://regex101.com/r/Gvb1iE/1
     * @var string
     */
    private const FILE_LATTE_REGEX = '#([\w\/"]+).latte#';

    public function getPriority(): int
    {
        return 150;
    }

    public function convertContent(string $content): string
    {
        // suffix: "_snippets/menu.latte" => "_snippets/menu.twig"
        return Strings::replace($content, self::FILE_LATTE_REGEX, '$1.twig');
    }
}
