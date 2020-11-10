<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\GitHubActionsWorkflow;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\ValueObject\Package;
use Symplify\SmartFileSystem\SmartFileInfo;

final class MissingPackageInWorkflowResolver
{
    /**
     * @param Package[] $packages
     * @return Package[]
     */
    public function resolveInFileInfo(array $packages, SmartFileInfo $workflowFileInfo): array
    {
        $missingPackages = [];

        foreach ($packages as $package) {
            $packageNameItemPattern = '#\-\s+' . preg_quote($package->getShortDirectory(), '#') . '\b#';

            if (Strings::match($workflowFileInfo->getContents(), $packageNameItemPattern)) {
                // not important
                continue;
            }

            $missingPackages[] = $package;
        }

        return $missingPackages;
    }
}
