<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\FileInfoDecorator;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\PHPUnitUpgrader\Tests\FileInfoDecorator\SetUpTearDownVoidFileInfoDecorator\SetUpTearDownVoidFileInfoDecoratorTest
 */
final class SetUpTearDownVoidFileInfoDecorator
{
    /**
     * @see https://regex101.com/r/HrcQQW/2
     * @var string
     */
    private const VOID_LESS_REGEX = '#(?<method>setUp|tearDown)\(\)\n#i';

    public function decorate(SmartFileInfo $fileInfo): string
    {
        return Strings::replace($fileInfo->getContents(), self::VOID_LESS_REGEX, "$1(): void\n");
    }
}
