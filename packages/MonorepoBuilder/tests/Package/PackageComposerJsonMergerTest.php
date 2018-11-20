<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger;
use Symplify\MonorepoBuilder\Tests\AbstractConfigAwareContainerTestCase;

final class PackageComposerJsonMergerTest extends AbstractConfigAwareContainerTestCase
{
    /**
     * @var PackageComposerJsonMerger
     */
    private $packageComposerJsonMerger;

    protected function setUp(): void
    {
        $this->packageComposerJsonMerger = $this->container->get(PackageComposerJsonMerger::class);
    }

    public function test(): void
    {
        $merged = $this->packageComposerJsonMerger->mergeFileInfos(
            $this->getFileInfosFromDirectory(__DIR__ . '/Source'),
            $this->container->getParameter('merge_sections')
        );

        $this->assertSame([
            'require' => [
                'rector/rector' => '^2.0',
                'phpunit/phpunit' => '^2.0',
                'symplify/symplify' => '^2.0',
            ],
            'autoload' => [
                'psr-4' => [
                    'Symplify\Statie\\' => 'src',
                    'Symplify\MonorepoBuilder\\' => 'src',
                ],
            ],
            'minimum-stability' => 'dev',
        ], $merged);
    }

    public function testUniqueRepositories(): void
    {
        $merged = $this->packageComposerJsonMerger->mergeFileInfos(
            $this->getFileInfosFromDirectory(__DIR__ . '/SourceUniqueRepositories'),
            ['repositories']
        );
        $this->assertSame([
            'repositories' => [[
                'type' => 'composer',
                'url' => 'https://packages.example.org/',
            ]],
        ], $merged);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/Source/config.yml';
    }

    /**
     * @return SplFileInfo[]
     */
    private function getFileInfosFromDirectory(string $directory): array
    {
        $iterator = Finder::create()->files()
            ->in($directory)
            ->name('*.json')
            ->getIterator();

        return iterator_to_array($iterator);
    }
}
