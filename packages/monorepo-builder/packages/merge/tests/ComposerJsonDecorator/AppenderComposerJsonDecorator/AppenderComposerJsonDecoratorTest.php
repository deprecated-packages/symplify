<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\AppenderComposerJsonDecorator;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator\AppenderComposerJsonDecorator;
use Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;

final class AppenderComposerJsonDecoratorTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var AppenderComposerJsonDecorator
     */
    private $appenderComposerJsonDecorator;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(MonorepoBuilderKernel::class, [__DIR__ . '/Source/appending_config.yaml']);

        $this->composerJsonFactory = self::$container->get(ComposerJsonFactory::class);
        $this->appenderComposerJsonDecorator = self::$container->get(AppenderComposerJsonDecorator::class);
    }

    public function test(): void
    {
        $composerJson = $this->createComposerJson(__DIR__ . '/Source/input.json');
        $this->appenderComposerJsonDecorator->decorate($composerJson);

        $this->assertComposerJsonEquals(__DIR__ . '/Source/expected.json', $composerJson);
    }
}
