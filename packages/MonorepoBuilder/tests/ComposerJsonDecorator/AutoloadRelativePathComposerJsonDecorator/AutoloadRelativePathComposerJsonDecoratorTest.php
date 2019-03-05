<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator\AutoloadRelativePathComposerJsonDecorator;

use PHPUnit\Framework\TestCase;
use Symplify\MonorepoBuilder\ComposerJsonDecorator\AutoloadRelativePathComposerJsonDecorator;
use Symplify\MonorepoBuilder\PackageComposerFinder;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;

final class AutoloadRelativePathComposerJsonDecoratorTest extends TestCase
{
    /**
     * @var mixed[]
     */
    private $composerJson = [
        'autoload' => [
            'psr-4' => [
                'App\\' => 'src',
                'Shopsys\\' => ['app/', 'src/Shopsys/'],
            ],
            'files' => ['src/SomeFile.php'],
            'classmap' => ['src/SomeClass.php'],
        ],
    ];

    /**
     * @var mixed[]
     */
    private $composerJsonWithOverlappingNamespaces = [
        'autoload' => [
            'psr-4' => [
                'App\\' => 'src',
                'App\\PackageB\\' => 'src',
                'Shopsys\\' => ['app/', 'src/Shopsys/'],
                'Shopsys\\PackageB\\' => ['app/', 'src/Shopsys/'],
            ],
        ],
    ];

    /**
     * @var mixed[]
     */
    private $composerJsonWithIdenticalNamespaces = [ 
        'autoload' => [ // TODO: This undecorated merged autoload is nonsense. PackageComposerJsonMerger should prefix autoload and autoload-dev
                        //   See also: CombineStringsToArrayJsonMergerTest::testIdenticalNamespaces
            'psr-4' => [
                'App\\Core\\' => ['src/core', 'src/core-extension'],
                'App\\Model\\' => ['src/interfaces', 'src/models'],
                'App\\Shared\\' => 'src/shared',
                'App\\Sub\\' => ['src/package-c', 'src/package-d'],
            ],
        ],
    ];

    /**
     * @var AutoloadRelativePathComposerJsonDecorator
     */
    private $autoloadRelativePathComposerJsonDecorator;

    protected function setUp(): void
    {
        $packageComposerFinder = new PackageComposerFinder([__DIR__ . '/Source'], new FinderSanitizer());

        $this->autoloadRelativePathComposerJsonDecorator = new AutoloadRelativePathComposerJsonDecorator(
            $packageComposerFinder
        );
    }

    public function test(): void
    {
        $decorated = $this->autoloadRelativePathComposerJsonDecorator->decorate($this->composerJson);

        $this->assertSame($this->getExpectedComposerJson(), $decorated);
    }

    public function testOverlappingNamespaces(): void
    {
        $packageComposerFinder = new PackageComposerFinder([
            __DIR__ . '/SourceOverlappingNamespaces/PackageA',
            __DIR__ . '/SourceOverlappingNamespaces/PackageB',
        ], new FinderSanitizer());

        $autoloadRelativePathComposerJsonDecorator = new AutoloadRelativePathComposerJsonDecorator(
            $packageComposerFinder
        );

        $decorated = $autoloadRelativePathComposerJsonDecorator->decorate($this->composerJsonWithOverlappingNamespaces);

        $this->assertSame($this->getExpectedComposerJsonWithOverlappingNamespaces(), $decorated);
    }

    public function testIdenticalNamespaces(): void
    {
        $packageComposerFinder = new PackageComposerFinder([
            __DIR__ . '/SourceIdenticalNamespaces/PackageA',
            __DIR__ . '/SourceIdenticalNamespaces/PackageB',
            __DIR__ . '/SourceIdenticalNamespaces/SubA/PackageC',
            __DIR__ . '/SourceIdenticalNamespaces/SubB/PackageD',
        ], new FinderSanitizer());

        $autoloadRelativePathComposerJsonDecorator = new AutoloadRelativePathComposerJsonDecorator(
            $packageComposerFinder
        );

        $decorated = $autoloadRelativePathComposerJsonDecorator->decorate($this->composerJsonWithIdenticalNamespaces);

        $this->assertSame($this->getExpectedComposerJsonWithIdenticalNamespaces(), $decorated);
    }

    /**
     * @return mixed[]
     */
    private function getExpectedComposerJson(): array
    {
        return [
            'autoload' => [
                'psr-4' => [
                    'App\\' => $this->getRelativeSourcePath() . '/src',
                    'Shopsys\\' => [
                        $this->getRelativeSourcePath() . '/app/',
                        $this->getRelativeSourcePath() . '/src/Shopsys/',
                    ],
                ],
                'files' => [$this->getRelativeSourcePath() . '/src/SomeFile.php'],
                'classmap' => [$this->getRelativeSourcePath() . '/src/SomeClass.php'],
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    private function getExpectedComposerJsonWithOverlappingNamespaces(): array
    {
        return [
            'autoload' => [
                'psr-4' => [
                    'App\\' => $this->getRelativeOverlappingSourcePath() . '/PackageA/src',
                    'App\\PackageB\\' => $this->getRelativeOverlappingSourcePath() . '/PackageB/src',
                    'Shopsys\\' => [
                        $this->getRelativeOverlappingSourcePath() . '/PackageA/app/',
                        $this->getRelativeOverlappingSourcePath() . '/PackageA/src/Shopsys/',
                    ],
                    'Shopsys\\PackageB\\' => [
                        $this->getRelativeOverlappingSourcePath() . '/PackageB/app/',
                        $this->getRelativeOverlappingSourcePath() . '/PackageB/src/Shopsys/',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    private function getExpectedComposerJsonWithIdenticalNamespaces(): array
    {
        return [
            'autoload' => [
                'psr-4' => [
                    'App\\Core\\' => [
                        $this->getRelativeIdenticalSourcePath() . '/PackageA/src/core',
                        $this->getRelativeIdenticalSourcePath() . '/PackageB/src/core-extension',
                    ],
                    'App\\Model\\' => [
                        $this->getRelativeIdenticalSourcePath() . '/PackageB/src/interfaces',
                        $this->getRelativeIdenticalSourcePath() . '/SubA/PackageB/src/models',
                    ],
                    'App\\Shared\\' => [
                        $this->getRelativeIdenticalSourcePath() . '/PackageA/src/shared',
                        $this->getRelativeIdenticalSourcePath() . '/PackageB/src/shared',
                    ],
                    'App\\Sub\\' => [
                        $this->getRelativeIdenticalSourcePath() . '/SubA/PackageC/src/model',
                        $this->getRelativeIdenticalSourcePath() . '/SubB/PackageD/src/model',
                    ],
                ],
            ],
        ];
    }

    private function getRelativeSourcePath(): string
    {
        $prefix = defined('SYMPLIFY_MONOREPO') ? 'packages/MonorepoBuilder/' : '';

        return $prefix . 'tests/ComposerJsonDecorator/AutoloadRelativePathComposerJsonDecorator/Source';
    }

    private function getRelativeOverlappingSourcePath(): string
    {
        $prefix = defined('SYMPLIFY_MONOREPO') ? 'packages/MonorepoBuilder/' : '';

        return $prefix . 'tests/ComposerJsonDecorator/AutoloadRelativePathComposerJsonDecorator/SourceOverlappingNamespaces';
    }

    private function getRelativeIdenticalSourcePath(): string
    {
        $prefix = defined('SYMPLIFY_MONOREPO') ? 'packages/MonorepoBuilder/' : '';

        return $prefix . 'tests/ComposerJsonDecorator/AutoloadRelativePathComposerJsonDecorator/SourceIdenticalNamespaces';
    }
}
