<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\PathResolver;

use Symplify\MonorepoBuilder\Merge\PathResolver\AutoloadPathNormalizer;
use Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AutoloadPathNormalizerTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var AutoloadPathNormalizer
     */
    private $autoloadPathNormalizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->autoloadPathNormalizer = self::$container->get(AutoloadPathNormalizer::class);
    }

    public function test(): void
    {
        $autoloadFileInfo = new SmartFileInfo(__DIR__ . '/AutoloadPathNormalizerSource/autoload.json');
        $composerJson = $this->createComposerJson($autoloadFileInfo);

        $this->autoloadPathNormalizer->normalizeAutoloadPaths($composerJson, $autoloadFileInfo);
        $this->assertComposerJsonEquals($this->getExpectedComposerJson(), $composerJson);
    }

    private function getExpectedComposerJson(): string
    {
        if (defined('SYMPLIFY_MONOREPO')) {
            return __DIR__ . '/AutoloadPathNormalizerSource/expected-autoload.json';
        }

        return __DIR__ . '/AutoloadPathNormalizerSource/split-expected-autoload.json';
    }
}
