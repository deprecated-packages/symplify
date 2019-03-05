<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

abstract class AbstractMergeTestCase extends AbstractKernelTestCase
{
    /**
     * @var PackageComposerJsonMerger
     */
    private $packageComposerJsonMerger;

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->packageComposerJsonMerger = self::$container->get(PackageComposerJsonMerger::class);
        $this->finderSanitizer = self::$container->get(FinderSanitizer::class);
    }

    /**
     * @param string|mixed[] $expected
     */
    public function doTestDirectoryMergeToFile(string $directoryWithJsonFiles, $expected): void
    {
        $merged = $this->packageComposerJsonMerger->mergeFileInfos(
            $this->getFileInfosFromDirectory($directoryWithJsonFiles)
        );

        if (is_array($expected)) {
            $expectedJson = $expected;
        } else {
            $expectedJson = $this->loadJsonFromFile($expected);
        }

        $this->assertSame($expectedJson, $merged);
    }

    /**
     * @return SmartFileInfo[]
     */
    private function getFileInfosFromDirectory(string $directory): array
    {
        $finder = Finder::create()->files()
            ->in($directory)
            ->name('*.json')
            ->sortByName();

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @return mixed[]
     */
    private function loadJsonFromFile(string $filePath): array
    {
        $fileContent = FileSystem::read($filePath);

        return Json::decode($fileContent, Json::FORCE_ARRAY);
    }
}
