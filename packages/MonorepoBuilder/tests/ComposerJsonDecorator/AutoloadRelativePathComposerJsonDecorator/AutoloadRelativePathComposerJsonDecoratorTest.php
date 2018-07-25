<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator\AutoloadRelativePathComposerJsonDecorator;

use PHPUnit\Framework\TestCase;
use Symplify\MonorepoBuilder\ComposerJsonDecorator\AutoloadRelativePathComposerJsonDecorator;
use Symplify\MonorepoBuilder\PackageComposerFinder;

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
     * @var AutoloadRelativePathComposerJsonDecorator
     */
    private $autoloadRelativePathComposerJsonDecorator;

    protected function setUp(): void
    {
        $packageComposerFinder = new PackageComposerFinder([__DIR__ . '/Source']);

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
        ]);

        $autoloadRelativePathComposerJsonDecorator = new AutoloadRelativePathComposerJsonDecorator(
            $packageComposerFinder
        );

        $decorated = $autoloadRelativePathComposerJsonDecorator->decorate($this->composerJsonWithOverlappingNamespaces);

        $this->assertSame($this->getExpectedComposerJsonWithOverlappingNamespaces(), $decorated);
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
}
