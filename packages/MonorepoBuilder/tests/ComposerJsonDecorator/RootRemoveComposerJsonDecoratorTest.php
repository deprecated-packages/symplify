<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator;

use PHPUnit\Framework\TestCase;
use Symplify\MonorepoBuilder\ComposerJsonDecorator\RootRemoveComposerJsonDecorator;

final class RootRemoveComposerJsonDecoratorTest extends TestCase
{
    /**
     * @var mixed[]
     */
    private $composerJson = [
        'name' => 'symfony/symfony',
        'require' => [
            'symfony/console' => 'v1.0.0',
            'symfony-friends/coding-standard' => 'v1.0.0',
            'rector/rector' => 'v1.0.0',
        ],
    ];

    /**
     * @var mixed[]
     */
    private $expectedComposerJson = [
        'name' => 'symfony/symfony',
        'require' => [
            'symfony-friends/coding-standard' => 'v1.0.0',
            'rector/rector' => 'v1.0.0',
        ],
    ];

    /**
     * @var RootRemoveComposerJsonDecorator
     */
    private $rootRemoveComposerJsonDecorator;

    protected function setUp(): void
    {
        $this->rootRemoveComposerJsonDecorator = new RootRemoveComposerJsonDecorator();
    }

    public function testNoSort(): void
    {
        $decorated = $this->rootRemoveComposerJsonDecorator->decorate($this->composerJson);

        $this->assertSame($this->expectedComposerJson, $decorated);
    }
}
