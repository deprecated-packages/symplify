<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Twig\Contract;

use Symplify\EasyCI\ValueObject\TemplateError;
use Symplify\SmartFileSystem\SmartFileInfo;

interface TwigTemplateAnalyzerInterface
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return TemplateError[]
     */
    public function analyze(array $fileInfos): array;
}
