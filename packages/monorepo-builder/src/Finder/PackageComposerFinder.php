<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\MonorepoBuilder\Tests\Finder\PackageComposerFinder\PackageComposerFinderTest
 */
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

    public function __construct(ParameterProvider $parameterProvider, FinderSanitizer $finderSanitizer)
    {
        $this->packageDirectories = $parameterProvider->provideArrayParameter(Option::PACKAGE_DIRECTORIES);
        $this->packageDirectoriesExcludes = $parameterProvider->provideArrayParameter(
            Option::PACKAGE_DIRECTORIES_EXCLUDES
        );
        $this->finderSanitizer = $finderSanitizer;
    }

    public function getRootPackageComposerFile(): SmartFileInfo
    {
        return new SmartFileInfo(getcwd() . DIRECTORY_SEPARATOR . 'composer.json');
    }

    /**
     * @return SmartFileInfo[]
     */
    public function getPackageComposerFiles(): array
    {
        $finder = Finder::create()
            ->files()
            ->in($this->packageDirectories)
            // sub-directory for wrapping to phar
            ->exclude('compiler')
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
