<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\PathsResolver;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
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
        $inputFileInfo = new SmartFileInfo(__DIR__ . '/AutoloadPathNormalizerSource/input.json');

        $composerJson = $this->composerJsonFactory->createFromFileInfo($inputFileInfo);
        $this->autoloadPathNormalizer->normalizeAutoloadPaths($composerJson, $inputFileInfo);

        $this->assertComposerJsonEquals($this->getExpectedComposerJson(), $composerJson);
    }

    private function getExpectedComposerJson(): ComposerJson
    {
        if (defined('SYMPLIFY_MONOREPO')) {
            return $this->createComposerJson(__DIR__ . '/AutoloadPathNormalizerSource/expected.json');
        }

        return $this->createComposerJson(__DIR__ . '/AutoloadPathNormalizerSource/split-expected.json');
    }
}
