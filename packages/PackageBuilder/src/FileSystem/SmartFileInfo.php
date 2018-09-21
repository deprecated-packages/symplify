<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\FileSystem;

use Nette\Utils\Strings;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\PackageBuilder\Exception\FileSystem\FileNotFoundException;
use function Safe\getcwd;
use function Safe\realpath;
use function Safe\sprintf;

final class SmartFileInfo extends SplFileInfo
{
    /**
     * @param mixed $filePath
     */
    public function __construct($filePath)
    {
        if (! file_exists($filePath)) {
            throw new FileNotFoundException(sprintf(
                'File path "%s" was not found while creating "%s" object.',
                $filePath,
                self::class
            ));
        }

        $relativeFilePath = Strings::substring(realpath($filePath), strlen(getcwd()) + 1);
        $relativeDirectoryPath = dirname($relativeFilePath);

        parent::__construct($filePath, $relativeDirectoryPath, $relativeFilePath);
    }
}
