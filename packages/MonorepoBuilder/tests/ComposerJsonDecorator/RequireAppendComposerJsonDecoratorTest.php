<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator;

use PHPUnit\Framework\TestCase;
use Symplify\MonorepoBuilder\ComposerJsonDecorator\RequireAppendComposerJsonDecorator;

final class RequireAppendComposerJsonDecoratorTest extends TestCase
{
    /**
     * @var mixed[]
     */
    private $composerJson = [
        'require-dev' => [
            'rector/rector' => 'v1.0.0',
        ],
    ];

    /**
     * @var mixed[]
     */
    private $expectedComposerJson = [
        'require-dev' => [
            'rector/rector' => 'v1.0.0',
            'phpstan/phpstan' => '^0.9',
            'tracy/tracy' => '^2.4',
            'slam/php-cs-fixer-extensions' => '^1.15',
        ],
    ];

    /**
     * @var RequireAppendComposerJsonDecorator
     */
    private $requireAppendComposerJsonDecorator;

    protected function setUp(): void
    {
        $this->requireAppendComposerJsonDecorator = new RequireAppendComposerJsonDecorator();
    }

    public function test(): void
    {
        $decorated = $this->requireAppendComposerJsonDecorator->decorate($this->composerJson);

        $this->assertSame($this->expectedComposerJson, $decorated);
    }
}
