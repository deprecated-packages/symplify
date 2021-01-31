<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\SortComposerJsonDecorator;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator\SortComposerJsonDecorator;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class SortComposerJsonDecoratorTest extends AbstractKernelTestCase
{
    /**
     * @var ComposerJson
     */
    private $composerJson;

    /**
     * @var SortComposerJsonDecorator
     */
    private $sortComposerJsonDecorator;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->composerJson = $this->createComposerJson();
        $this->sortComposerJsonDecorator = $this->getService(SortComposerJsonDecorator::class);
    }

    public function test(): void
    {
        $this->sortComposerJsonDecorator->decorate($this->composerJson);

        $this->assertSame(
            [
                'random-this',
                'random-that',
                ComposerJsonSection::REQUIRE,
                ComposerJsonSection::REQUIRE_DEV,
                ComposerJsonSection::AUTOLOAD,
                ComposerJsonSection::AUTOLOAD_DEV,
            ],
            $this->composerJson->getOrderedKeys()
        );
    }

    private function createComposerJson(): ComposerJson
    {
        /** @var ComposerJsonFactory $composerJsonFactory */
        $composerJsonFactory = $this->getService(ComposerJsonFactory::class);

        return $composerJsonFactory->createFromArray([
            'random-this' => [],
            ComposerJsonSection::AUTOLOAD_DEV => [],
            ComposerJsonSection::AUTOLOAD => [],
            'random-that' => [],
            ComposerJsonSection::REQUIRE_DEV => [],
            ComposerJsonSection::REQUIRE => [],
        ]);
    }
}
