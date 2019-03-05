<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

final class CombineStringsToArrayJsonMergerTest extends AbstractMergeTestCase
{
    public function testIdenticalNamespaces(): void
    {
        $expectedJson = [
            'autoload' => [
                'psr-4' => [
                    'App\\Core\\' => [
                        $this->getRelativeSourcePath() . 'PackageA/src/core',
                        $this->getRelativeSourcePath() . 'PackageB/src/core-extension'],
                    'App\\Model\\' => [
                        $this->getRelativeSourcePath() . 'PackageB/src/interfaces',
                        $this->getRelativeSourcePath() . 'SubA/PackageC/src/models'
                    ],
                    'App\\Shared\\' => [
                        $this->getRelativeSourcePath() . 'PackageA/src/shared',
                        $this->getRelativeSourcePath() . 'PackageB/src/shared',
                    ],
                    'App\\Sub\\' => [
                        $this->getRelativeSourcePath() . 'SubA/PackageC/src/package-c',
                        $this->getRelativeSourcePath() . 'SubB/PackageD/src/package-d'
                    ],
                ],
            ],
        ];

        $this->doTestDirectoryMergeToFile(__DIR__ . '/SourceIdenticalNamespaces', $expectedJson);
    }

    private function getRelativeSourcePath(): string
    {
        $prefix = defined('SYMPLIFY_MONOREPO') ? 'packages/MonorepoBuilder/' : '';

        return $prefix . 'tests/Package/SourceIdenticalNamespaces/';
    }
}
