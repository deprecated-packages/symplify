<?php

declare(strict_types=1);

namespace Symplify\EasyCI\StaticDetector\CurrentProvider;

use Symplify\SmartFileSystem\SmartFileInfo;

final class CurrentFileInfoProvider
{
    private SmartFileInfo $smartFileInfo;

    public function setCurrentFileInfo(SmartFileInfo $smartFileInfo): void
    {
        $this->smartFileInfo = $smartFileInfo;
    }

    public function getSmartFileInfo(): SmartFileInfo
    {
        return $this->smartFileInfo;
    }
}
