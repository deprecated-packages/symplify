<?php

declare(strict_types=1);

namespace Symplify\VendorPatches;

use Nette\Utils\Strings;
use Symplify\VendorPatches\ValueObject\OldAndNewFileInfo;

final class PatchFileFactory
{
    public function createPatchFilePath(OldAndNewFileInfo $oldAndNewFileInfo, string $vendorDirectory): string
    {
        $newFileInfo = $oldAndNewFileInfo->getNewFileInfo();

        $inVendorRelativeFilePath = $newFileInfo->getRelativeFilePathFromDirectory($vendorDirectory);

        $relativeFilePathWithoutSuffix = Strings::lower($inVendorRelativeFilePath);
        $pathFileName = Strings::webalize($relativeFilePathWithoutSuffix) . '.patch';

        return 'patches' . DIRECTORY_SEPARATOR . $pathFileName;
    }
}
