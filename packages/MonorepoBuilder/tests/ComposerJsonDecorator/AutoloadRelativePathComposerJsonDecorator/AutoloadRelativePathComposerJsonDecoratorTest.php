<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator\AutoloadRelativePathComposerJsonDecoratorTest;

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
    private $expectedComposerJson = [
        'autoload' => [
            'psr-4' => [
                'App\\' => self::RELATIVE_SOURCE_PATH . '/src',
                'Shopsys\\' => [
                    self::RELATIVE_SOURCE_PATH . '/app/',
                    self::RELATIVE_SOURCE_PATH . '/src/Shopsys/',
                ],
            ],
            'files' => [self::RELATIVE_SOURCE_PATH . '/src/SomeFile.php'],
            'classmap' => [self::RELATIVE_SOURCE_PATH . '/src/SomeClass.php'],
        ],
    ];

    /**
     * @var AutoloadRelativePathComposerJsonDecorator
     */
    private $autoloadRelativePathComposerJsonDecorator;

    /**
     * @var string
     */
    private const RELATIVE_SOURCE_PATH = 'packages/MonorepoBuilder/tests/ComposerJsonDecorator/AutoloadRelativePathComposerJsonDecorator/Source';

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

        $this->assertSame($this->expectedComposerJson, $decorated);
    }
}
