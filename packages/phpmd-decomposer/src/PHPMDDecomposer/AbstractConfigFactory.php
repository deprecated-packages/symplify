<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\PHPMDDecomposer;

use DOMDocument;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class AbstractConfigFactory
{
    protected function createDOMDocumentFromXmlFileInfo(SmartFileInfo $smartFileInfo): DOMDocument
    {
        $domDocument = new DOMDocument();
        $domDocument->loadXML($smartFileInfo->getContents());

        return $domDocument;
    }
}
