<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte\Contract;

use Symplify\EasyCI\Latte\ValueObject\LatteError;
use Symplify\SmartFileSystem\SmartFileInfo;

interface LatteAnalyzerInterface
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return LatteError[]
     */
    public function analyze(array $fileInfos): array;
}
