<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Merge\PathResolver;

use Symplify\MonorepoBuilder\Merge\PathResolver\AutoloadPathNormalizer;
use Symplify\MonorepoBuilder\Tests\Merge\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AutoloadPathNormalizerTest extends AbstractComposerJsonDecoratorTest
{
    private AutoloadPathNormalizer $autoloadPathNormalizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->autoloadPathNormalizer = $this->getService(AutoloadPathNormalizer::class);
    }

    public function test(): void
    {
        if (! defined('SYMPLIFY_MONOREPO')) {
            $this->markTestSkipped('Already tested on monorepo');
        }

        $autoloadFileInfo = new SmartFileInfo(__DIR__ . '/AutoloadPathNormalizerSource/autoload.json');
        $composerJson = $this->createComposerJson($autoloadFileInfo);

        $this->autoloadPathNormalizer->normalizeAutoloadPaths($composerJson, $autoloadFileInfo);
        $this->assertComposerJsonEquals(
            __DIR__ . '/AutoloadPathNormalizerSource/expected-autoload.json',
            $composerJson
        );
    }
}
