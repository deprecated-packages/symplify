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

    private function getRelativeSourcePath(): string
    {
        $prefix = defined('SYMPLIFY_MONOREPO') ? 'packages/MonorepoBuilder/' : '';

        return $prefix . 'tests/ComposerJsonDecorator/AutoloadRelativePathComposerJsonDecorator/Source';
    }
}
