<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator;

use PHPUnit\Framework\TestCase;
use Symplify\MonorepoBuilder\ComposerJsonDecorator\RemoverComposerJsonDecorator;

final class RemoverComposerJsonDecoratorTest extends TestCase
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
     * @var RemoverComposerJsonDecorator
     */
    private $removerComposerJsonDecorator;

    protected function setUp(): void
    {
        $this->removerComposerJsonDecorator = new RemoverComposerJsonDecorator([
            'require' => [
                'phpunit/phpunit' => 'v1.0.0',
            ],
        ]);
    }

    public function test(): void
    {
        $decorated = $this->removerComposerJsonDecorator->decorate($this->composerJson);

        $this->assertSame($this->expectedComposerJson, $decorated);
    }
}
