<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\ComposerJsonObject\ComposerJsonFactory;
use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator\RemoverComposerJsonDecorator;

final class RemoverComposerJsonDecoratorTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var ComposerJson
     */
    private $composerJson;

    /**
     * @var ComposerJson
     */
    private $expectedComposerJson;

    /**
     * @var RemoverComposerJsonDecorator
     */
    private $removerComposerJsonDecorator;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(MonorepoBuilderKernel::class, [__DIR__ . '/Source/removing-config.yaml']);

        $this->removerComposerJsonDecorator = self::$container->get(RemoverComposerJsonDecorator::class);

        $this->composerJson = $this->createMainComposerJson();
        $this->expectedComposerJson = $this->createExpectedComposerJson();

//        $composerJsonToRemove = $this->createComposerJsonToRemove();

//        $this->removerComposerJsonDecorator->setComposerJsonToRemove($composerJsonToRemove);
    }

    public function test(): void
    {
        $this->removerComposerJsonDecorator->decorate($this->composerJson);

        $this->assertComposerJsonEquals($this->expectedComposerJson, $this->composerJson);
    }

    private function createMainComposerJson(): ComposerJson
    {
        /** @var ComposerJsonFactory $composerJsonFactory */
        $composerJsonFactory = self::$container->get(ComposerJsonFactory::class);

        $composerJsonData = [
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

        return $composerJsonFactory->createFromArray($composerJsonData);
    }

    private function createExpectedComposerJson(): ComposerJson
    {
        /** @var ComposerJsonFactory $composerJsonFactory */
        $composerJsonFactory = self::$container->get(ComposerJsonFactory::class);

        $expectedComposerJson = [
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

        return $composerJsonFactory->createFromArray($expectedComposerJson);
    }
}
