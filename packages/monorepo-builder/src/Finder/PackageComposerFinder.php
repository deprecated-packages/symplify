<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PackageComposerFinder
{
    /**
     * @var string[]
     */
    private $packageDirectories = [];

    /**
     * @var string[]
     */
    private $packageDirectoriesExcludes = [];

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    /**
     * @param string[] $packageDirectories
     * @param string[] $packageDirectoriesExcludes
     */
    public function __construct(
        array $packageDirectories,
        array $packageDirectoriesExcludes,
        FinderSanitizer $finderSanitizer
    ) {
        $this->packageDirectories = $packageDirectories;
        $this->packageDirectoriesExcludes = $packageDirectoriesExcludes;
        $this->finderSanitizer = $finderSanitizer;
    }

    public function getRootPackageComposerFile(): SmartFileInfo
    {
        return new SmartFileInfo('composer.json');
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getPackageComposerFiles(): array
    {
        $finder = Finder::create()
            ->files()
            ->in($this->packageDirectories)
            // "init" command template data
            ->exclude('templates')
            ->exclude('vendor')
            ->exclude('node_modules')
            ->name('composer.json');

        foreach ($this->packageDirectoriesExcludes as $excludeFolder) {
            $finder->exclude($excludeFolder);
        }

        if (! $this->isPHPUnit()) {
            $finder->notPath('#tests#');
        }

        return $this->finderSanitizer->sanitize($finder);
    }

    private function isPHPUnit(): bool
    {
        // defined by PHPUnit
        return defined('PHPUNIT_COMPOSER_INSTALL') || defined('__PHPUNIT_PHAR__');
    }
}
