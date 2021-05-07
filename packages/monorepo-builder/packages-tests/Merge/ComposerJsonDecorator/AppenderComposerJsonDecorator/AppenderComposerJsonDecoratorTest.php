<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Merge\ComposerJsonDecorator\AppenderComposerJsonDecorator;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator\AppenderComposerJsonDecorator;
use Symplify\MonorepoBuilder\Tests\Merge\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;

final class AppenderComposerJsonDecoratorTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var AppenderComposerJsonDecorator
     */
    private $appenderComposerJsonDecorator;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(MonorepoBuilderKernel::class, [__DIR__ . '/Source/appending_config.php']);

        $this->composerJsonFactory = $this->getService(ComposerJsonFactory::class);
        $this->appenderComposerJsonDecorator = $this->getService(AppenderComposerJsonDecorator::class);
    }

    public function test(): void
    {
        $composerJson = $this->createComposerJson(__DIR__ . '/Source/input.json');
        $this->appenderComposerJsonDecorator->decorate($composerJson);

        $this->assertComposerJsonEquals(__DIR__ . '/Source/expected.json', $composerJson);
    }
}
