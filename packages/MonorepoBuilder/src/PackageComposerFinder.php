<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class PackageComposerFinder
{
    /**
     * @var string[]
     */
    private $packageDirectories = [];

    /**
     * @param string[] $packageDirectories
     */
    public function __construct(array $packageDirectories)
    {
        $this->packageDirectories = $packageDirectories;
    }

    public function getRootPackageComposerFile(): SplFileInfo
    {
        return new SplFileInfo('composer.json', '', 'composer.json');
    }

    /**
     * @return SplFileInfo[]
     */
    public function getPackageComposerFiles(): array
    {
        $finder = Finder::create()
            ->files()
            ->in($this->packageDirectories)
            ->exclude('templates') // "init" command template data
            ->name('composer.json');

        if (! $this->isPHPUnit()) {
            $finder->notPath('#tests#');
        }

        return iterator_to_array($finder->getIterator());
    }

    private function isPHPUnit(): bool
    {
        // defined by PHPUnit
        return defined('PHPUNIT_COMPOSER_INSTALL') || defined('__PHPUNIT_PHAR__');
    }
}
