<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class PackageComposerJsonMergerTest extends AbstractKernelTestCase
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

    public function test(): void
    {
        $merged = $this->packageComposerJsonMerger->mergeFileInfos(
            $this->getFileInfosFromDirectory(__DIR__ . '/Source')
        );

        $this->assertSame([
            'require' => [
                'phpunit/phpunit' => '^2.0',
                'rector/rector' => '^2.0',
                'symplify/symplify' => '^2.0',
            ],
            'autoload' => [
                'psr-4' => [
                    'Symplify\MonorepoBuilder\\' => 'src',
                    'Symplify\Statie\\' => 'src',
                ],
            ],
        ], $merged);
    }

    public function testUniqueRepositories(): void
    {
        $merged = $this->packageComposerJsonMerger->mergeFileInfos(
            $this->getFileInfosFromDirectory(__DIR__ . '/SourceUniqueRepositories')
        );
        $this->assertSame([
            'repositories' => [[
                'type' => 'composer',
                'url' => 'https://packages.example.org/',
            ]],
        ], $merged);
    }

    /**
     * @return SmartFileInfo[]
     */
    private function getFileInfosFromDirectory(string $directory): array
    {
        $finder = Finder::create()->files()
            ->in($directory)
            ->name('*.json');

        return $this->finderSanitizer->sanitize($finder);
    }
}
