<?php

declare(strict_types=1);

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
        'autoload-dev' => [
            'psr-4' => [
                'Symplify\Tests\\' => 'tests',
                'Symplify\SuperTests\\' => 'super-tests',
            ],
            'files' => ['src/SomeFile.php', 'src/KeepFile.php'],
        ],
    ];

    /**
     * @var mixed[]
     */
    private $expectedComposerJson = [
        'require' => [
            'rector/rector' => 'v1.0.0',
        ],
        'autoload-dev' => [
            'psr-4' => [
                'Symplify\SuperTests\\' => 'super-tests',
            ],
            'files' => [
                1 => 'src/KeepFile.php',
            ],
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
                'phpunit/phpunit' => '*',
            ],
            'autoload-dev' => [
                'psr-4' => [
                    'Symplify\Tests\\' => 'tests',
                ],
                'files' => ['src/SomeFile.php'],
            ],
        ]);
    }

    public function test(): void
    {
        $decorated = $this->removerComposerJsonDecorator->decorate($this->composerJson);

        $this->assertSame($this->expectedComposerJson, $decorated);
    }
}
