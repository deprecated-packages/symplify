<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonMerger;

use Symplify\MonorepoBuilder\Merge\PathResolver\AutoloadPathNormalizer;
use Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;

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
        $this->autoloadPathNormalizer->normalizeAutoloadPaths();
    }
}
