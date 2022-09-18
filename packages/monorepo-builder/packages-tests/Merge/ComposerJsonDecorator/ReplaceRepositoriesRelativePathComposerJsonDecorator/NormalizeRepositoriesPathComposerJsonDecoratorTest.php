<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Merge\ComposerJsonDecorator\ReplaceRepositoriesRelativePathComposerJsonDecorator;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Kernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator\NormalizeRepositoriesPathComposerJsonDecorator;
use Symplify\MonorepoBuilder\Tests\Merge\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;

final class NormalizeRepositoriesPathComposerJsonDecoratorTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var array
     */
    private const COMPOSER_JSON_DATA = [
        ComposerJsonSection::REPOSITORIES => [
            [
                'type' => 'artifact',
                'url' => 'path/to/directory/with/zips/',

            ],
            [
                'type' => 'path',
                'url' => '../../libs/*/',
            ],
        ],
    ];

    private ComposerJson $composerJson;

    private ComposerJson $expectedComposerJson;

    private NormalizeRepositoriesPathComposerJsonDecorator $replaceRepositoriesRelativePathComposerJsonDecorator;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->replaceRepositoriesRelativePathComposerJsonDecorator = $this->getService(NormalizeRepositoriesPathComposerJsonDecorator::class);
        $this->composerJson = $this->createMainComposerJson();
        $this->expectedComposerJson = $this->createExpectedComposerJson();
    }

    public function test(): void
    {
        $this->replaceRepositoriesRelativePathComposerJsonDecorator->decorate($this->composerJson);

        $this->assertComposerJsonEquals($this->expectedComposerJson, $this->composerJson);
    }

    private function createMainComposerJson(): ComposerJson
    {
        /** @var ComposerJsonFactory $composerJsonFactory */
        $composerJsonFactory = $this->getService(ComposerJsonFactory::class);

        return $composerJsonFactory->createFromArray(self::COMPOSER_JSON_DATA);
    }

    private function createExpectedComposerJson(): ComposerJson
    {
        /** @var ComposerJsonFactory $composerJsonFactory */
        $composerJsonFactory = $this->getService(ComposerJsonFactory::class);

        $expectedComposerJson = [
            ComposerJsonSection::REPOSITORIES => [
                [
                    'type' => 'artifact',
                    'url' => 'path/to/directory/with/zips/',
                ],
                [
                    'type' => 'path',
                    'url' => 'libs/*/',
                ],
            ],
        ];

        return $composerJsonFactory->createFromArray($expectedComposerJson);
    }
}
