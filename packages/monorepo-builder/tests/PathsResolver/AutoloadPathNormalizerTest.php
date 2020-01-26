<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\PathsResolver;

use Symplify\MonorepoBuilder\ComposerJsonObject\ComposerJsonFactory;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\PathResolver\AutoloadPathNormalizer;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AutoloadPathNormalizerTest extends AbstractKernelTestCase
{
    /**
     * @var AutoloadPathNormalizer
     */
    private $autoloadPathNormalizer;

    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);
        $this->autoloadPathNormalizer = self::$container->get(AutoloadPathNormalizer::class);

        $this->composerJsonFactory = self::$container->get(ComposerJsonFactory::class);
    }

    public function test(): void
    {
        $inputFileInfo = new SmartFileInfo(__DIR__ . '/AutoloadPathNormalizerSource/input.json');

        $composerJson = $this->composerJsonFactory->createFromFileInfo($inputFileInfo);

        $this->autoloadPathNormalizer->normalizeAutoloadPaths($composerJson, $inputFileInfo);

        $this->assertSame([
            'psr-4' => [
                'App\\' => 'packages/monorepo-builder/tests/PathsResolver/AutoloadPathNormalizerSource/src',
            ],
        ], $composerJson->getAutoload());
    }
}
