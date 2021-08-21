<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Twig\Contract;

use Symplify\SmartFileSystem\SmartFileInfo;

interface TwigTemplateAnalyzerInterface
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return \Symplify\EasyCI\Contract\ValueObject\TemplateErrorInterface[]
     */
    public function analyze(array $fileInfos): array;
}
