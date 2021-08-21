<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte\Contract;

use Symplify\EasyCI\Contract\ValueObject\TemplateErrorInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

interface LatteTemplateAnalyzerInterface
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return TemplateErrorInterface[]
     */
    public function analyze(array $fileInfos): array;
}
