<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

final class CombineStringsToArrayJsonMergerTest extends AbstractMergeTestCase
{
    public function testSharedNamespaces(): void
    {
        $expectedJson = [
            'autoload' => [
                'psr-4' => [
                    'ACME\Another\\' => 'packages/A',
                    'ACME\Model\Core\\' => ['packages/A', 'packages/B'],
                    'ACME\\YetAnother\\' => ['packages/A'],
                    'ACME\\YetYetAnother\\' => 'packages/A',
                ],
            ],
        ];

        $this->doTestDirectoryMergeToFile(__DIR__ . '/SourceAutoloadSharedNamespaces', $expectedJson);
    }

    public function testIdenticalNamespaces(): void
    {
        // TODO: define expected behaviour; currently everything we know is: this ain't of any use
        $NOTexpectedJson = [
            'autoload' => [
                'psr-4' => [
                    'App\\Core\\' => ['src/core', 'src/core-extension'],
                    'App\\Model\\' => ['src/interfaces', 'src/models'],
                    'App\\Shared\\' => 'src/shared',
                    'App\\Sub\\' => ['src/package-c', 'src/package-d'],
                ],
            ],
        ];

        $merged = $this->packageComposerJsonMerger->mergeFileInfos(
            $this->getFileInfosFromDirectory(__DIR__ . '/SourceIdenticalNamespaces')
        );

        $this->assertNotSame($NOTexpectedJson, $merged);
    }
}
