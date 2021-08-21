<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Twig\Contract;

use Symplify\EasyCI\Contract\ValueObject\TemplateErrorInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

interface TwigTemplateAnalyzerInterface
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return TemplateErrorInterface[]
     */
    public function analyze(array $fileInfos): array;
}
