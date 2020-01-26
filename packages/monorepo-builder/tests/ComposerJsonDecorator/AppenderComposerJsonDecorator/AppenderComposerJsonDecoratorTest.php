<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator\AppenderComposerJsonDecorator;

use Symplify\MonorepoBuilder\ComposerJsonDecorator\AppenderComposerJsonDecorator;
use Symplify\MonorepoBuilder\ComposerJsonObject\ComposerJsonFactory;
use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;

final class AppenderComposerJsonDecoratorTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var ComposerJson
     */
    private $expectedComposerJson;

    /**
     * @var ComposerJson
     */
    private $composerJson;

    /**
     * @var AppenderComposerJsonDecorator
     */
    private $appenderComposerJsonDecorator;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(MonorepoBuilderKernel::class, [__DIR__ . '/Source/appending_config.yaml']);

        $this->composerJsonFactory = self::$container->get(ComposerJsonFactory::class);
        $this->appenderComposerJsonDecorator = self::$container->get(AppenderComposerJsonDecorator::class);

        $this->prepareComposerJsons();
    }

    public function test(): void
    {
        $this->appenderComposerJsonDecorator->decorate($this->composerJson);

        $this->assertComposerJsonEquals($this->expectedComposerJson, $this->composerJson);
    }

    private function prepareComposerJsons(): void
    {
        $this->composerJson = $this->createComposerJson(__DIR__ . '/Source/input.json');
        $this->expectedComposerJson = $this->createComposerJson(__DIR__ . '/Source/expected.json');
    }
}
