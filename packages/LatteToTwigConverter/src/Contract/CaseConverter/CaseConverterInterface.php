<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\Contract\CaseConverter;

interface CaseConverterInterface
{
    public function convertContent(string $content): string;

    /**
     * Higher priorities are executed first.
     */
    public function getPriority(): int;
}
