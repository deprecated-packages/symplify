<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger;

final class PackageComposerJsonMergerTest extends TestCase
{
    /**
     * @var PackageComposerJsonMerger
     */
    private $packageComposerJsonMerger;

    protected function setUp(): void
    {
        $this->packageComposerJsonMerger = new PackageComposerJsonMerger();
    }

    public function test(): void
    {
        $iterator = Finder::create()->files()
            ->in(__DIR__ . '/Source')
            ->getIterator();

        $files = iterator_to_array($iterator);

        $merged = $this->packageComposerJsonMerger->mergeFileInfos($files, ['require']);

        $this->assertSame([
            'require' => [
                'symplify/symplify' => '^2.0',
                'rector/rector' => '^2.0'
            ]
        ], $merged);
    }


//    /**
//     * @param mixed[] $composerPackageFileInfos
//     * @param string[] $sections
//     * @return string[]
//     */
//    public function mergeFileInfos(array $composerPackageFileInfos, array $sections): array
//    {
//        $merged = [];
//
//        foreach ($composerPackageFileInfos as $packageFile) {
//            $packageComposerJson = Json::decode($packageFile->getContents(), Json::FORCE_ARRAY);
//
//            foreach ($sections as $section) {
//                if (! isset($packageComposerJson[$section])) {
//                    continue;
//                }
//
//                $merged[$section] = array_merge($collected[$section] ?? [], $packageComposerJson[$section]);
//            }
//        }
//
//        return $merged;
//    }
}
