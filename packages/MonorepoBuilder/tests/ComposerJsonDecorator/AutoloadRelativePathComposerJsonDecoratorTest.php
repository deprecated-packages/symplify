<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator;

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
            ],
            'files' => ['src/SomeFile.php'],
            'classmap' => ['src/SomeClass.php'],
        ],
    ];

    /**
     * @var mixed[]
     */
    private $expectedComposerJson = [
        'autoload' => [
            'psr-4' => [
                'App\\' => 'packages/MonorepoBuilder/tests/ComposerJsonDecorator/AutoloadRelativePathComposerJsonDecoratorSource/src',
            ],
            'files' => [
                'packages/MonorepoBuilder/tests/ComposerJsonDecorator/AutoloadRelativePathComposerJsonDecoratorSource/src/SomeFile.php',
            ],
            'classmap' => [
                'packages/MonorepoBuilder/tests/ComposerJsonDecorator/AutoloadRelativePathComposerJsonDecoratorSource/src/SomeClass.php',
            ],
        ],
    ];

    /**
     * @var AutoloadRelativePathComposerJsonDecorator
     */
    private $autoloadRelativePathComposerJsonDecorator;

    protected function setUp(): void
    {
        $packageComposerFinder = new PackageComposerFinder([
            __DIR__ . '/AutoloadRelativePathComposerJsonDecoratorSource',
        ]);

        $this->autoloadRelativePathComposerJsonDecorator = new AutoloadRelativePathComposerJsonDecorator(
            $packageComposerFinder
        );
    }

    public function test(): void
    {
        $decorated = $this->autoloadRelativePathComposerJsonDecorator->decorate($this->composerJson);

        $this->assertSame($this->expectedComposerJson, $decorated);
    }
}
