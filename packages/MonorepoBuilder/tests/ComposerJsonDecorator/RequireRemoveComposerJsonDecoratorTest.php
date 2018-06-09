<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator;

use PHPUnit\Framework\TestCase;
use Symplify\MonorepoBuilder\ComposerJsonDecorator\RequireRemoveComposerJsonDecorator;

final class RequireRemoveComposerJsonDecoratorTest extends TestCase
{
    /**
     * @var mixed[]
     */
    private $composerJson = [
        'require' => [
            'phpunit/phpunit' => 'v1.0.0',
            'rector/rector' => 'v1.0.0',
        ],
    ];

    /**
     * @var mixed[]
     */
    private $expectedComposerJson = [
        'require' => [
            'rector/rector' => 'v1.0.0',
        ],
    ];

    /**
     * @var RequireRemoveComposerJsonDecorator
     */
    private $requireRemoveComposerJsonDecorator;

    protected function setUp(): void
    {
        $this->requireRemoveComposerJsonDecorator = new RequireRemoveComposerJsonDecorator();
    }

    public function test(): void
    {
        $decorated = $this->requireRemoveComposerJsonDecorator->decorate($this->composerJson);

        $this->assertSame($this->expectedComposerJson, $decorated);
    }
}
