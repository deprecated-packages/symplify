<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

final class CombineStringsToArrayJsonMergerTest extends AbstractMergeTestCase
{
    public function testIdenticalNamespaces(): void
    {
        $expectedJson = [
            'autoload' => [
                'classmap' => [
                    $this->getRelativeSourcePath() . 'PackageA/Something.php',
                    $this->getRelativeSourcePath() . 'PackageA/lib/',
                    $this->getRelativeSourcePath() . 'PackageA/src/',
                ],
                'exclude-from-classmap' => [
                    $this->getRelativeSourcePath() . 'PackageA/Tests/',
                    $this->getRelativeSourcePath() . 'PackageA/test/',
                    $this->getRelativeSourcePath() . 'PackageA/tests/',
                ],
                'files' => [
                    $this->getRelativeSourcePath() . 'PackageA/src/MyLibrary/functions.php',
                ],
                'psr-0' => [
                    '' => $this->getRelativeSourcePath() . 'PackageA/src/',
                    'Monolog\\' => $this->getRelativeSourcePath() . 'PackageA/src/',
                    'UniqueGlobalClass' => $this->getRelativeSourcePath() . 'PackageA/',
                    'Vendor\\Namespace\\' => [
                        $this->getRelativeSourcePath() . 'PackageA/lib/',
                        $this->getRelativeSourcePath() . 'PackageA/src/',
                    ],
                    'Vendor_Namespace_' => $this->getRelativeSourcePath() . 'PackageA/src/',
                ],
                'psr-4' => [
                    'App\\Collection\\' => [
                        $this->getRelativeSourcePath() . 'PackageA/src/collection',
                        $this->getRelativeSourcePath() . 'PackageB/src/collection',
                    ],
                    'App\\Core\\' => [
                        $this->getRelativeSourcePath() . 'PackageA/src/core',
                        $this->getRelativeSourcePath() . 'PackageB/src/core-extension',
                    ],
                    'App\\FixedArray\\' => [
                        $this->getRelativeSourcePath() . 'PackageA/src/array',
                        $this->getRelativeSourcePath() . 'PackageA/src/list',
                    ],
                    'App\\Model\\' => [
                        $this->getRelativeSourcePath() . 'PackageB/src/interfaces',
                        $this->getRelativeSourcePath() . 'SubA/PackageC/src/models',
                    ],
                    'App\\Shared\\' => [
                        $this->getRelativeSourcePath() . 'PackageA/src/shared',
                        $this->getRelativeSourcePath() . 'PackageB/src/shared',
                    ],
                    'App\\Sub\\' => [
                        $this->getRelativeSourcePath() . 'SubA/PackageC/src/package-c',
                        $this->getRelativeSourcePath() . 'SubB/PackageD/src/package-d',
                    ],
                    'App\\YetAnother\\' => [
                        $this->getRelativeSourcePath() . 'PackageA/src',
                        $this->getRelativeSourcePath() . 'PackageB/src',
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
