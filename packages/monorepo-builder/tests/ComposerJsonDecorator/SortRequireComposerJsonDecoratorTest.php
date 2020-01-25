<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\ComposerJsonObject\ComposerJsonFactory;
use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class SortRequireComposerJsonDecoratorTest extends AbstractKernelTestCase
{
    /**
     * @var ComposerJson
     */
    private $composerJson;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        /** @var ComposerJsonFactory $composerJsonFactory */
        $composerJsonFactory = self::$container->get(ComposerJsonFactory::class);

        $this->composerJson = $composerJsonFactory->createFromArray([
            'require' => [
                'b' => 'v1.0.0',
                'a' => 'v1.0.0',
            ],
        ]);
    }

    public function testSort(): void
    {
        $this->assertSame([
            'a' => 'v1.0.0',
            'b' => 'v1.0.0',
        ], $this->composerJson->getRequire());
    }
}
