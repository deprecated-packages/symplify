<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator;

use PHPUnit\Framework\TestCase;
use Symplify\MonorepoBuilder\ComposerJsonDecorator\AppenderComposerJsonDecorator;

final class AppenderComposerJsonDecoratorTest extends TestCase
{
    /**
     * @var mixed[]
     */
    private $composerJson = [
        'require-dev' => [
            'rector/rector' => 'v1.0.0',
        ],
        'autoload' => [
            'psr-4' => [
                'App\\' => 'src',
            ],
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
        'autoload' => [
            'psr-4' => [
                'App\\' => 'src',
            ],
        ],
    ];

    /**
     * @var AppenderComposerJsonDecorator
     */
    private $appenderComposerJsonDecorator;

    protected function setUp(): void
    {
        $this->appenderComposerJsonDecorator = new AppenderComposerJsonDecorator([
            'require-dev' => [
                'phpstan/phpstan' => '^0.9',
                'tracy/tracy' => '^2.4',
                'slam/php-cs-fixer-extensions' => '^1.15',
            ],
            'autoload' => [
                'psr-4' => [
                    'App\\' => 'src',
                    'Symplify\Tests\\' => 'tests',
                ],
            ],
        ]);
    }

    public function test(): void
    {
        $decorated = $this->appenderComposerJsonDecorator->decorate($this->composerJson);

        $this->assertSame($this->expectedComposerJson, $decorated);
    }
}
