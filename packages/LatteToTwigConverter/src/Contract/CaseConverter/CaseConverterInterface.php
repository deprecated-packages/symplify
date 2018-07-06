<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\Contract\CaseConverter;

interface CaseConverterInterface
{
    public function convertContent(string $content): string;
}
