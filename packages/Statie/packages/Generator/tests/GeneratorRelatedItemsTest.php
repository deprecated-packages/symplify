<?php

declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests;

use Symplify\Statie\Generator\RelatedItemsResolver;
use Symplify\Statie\HttpKernel\StatieKernel;

/**
 * @requires PHP < 7.4
 */
final class GeneratorRelatedItemsTest extends AbstractGeneratorTest
{
    /**
     * @var RelatedItemsResolver
     */
    private $relatedItemsResolver;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(StatieKernel::class, [__DIR__ . '/GeneratorSource/statie.yml']);

        parent::setUp();

        $this->relatedItemsResolver = self::$container->get(RelatedItemsResolver::class);
    }

    public function testRelatedItems(): void
    {
        $this->generator->run();
        $posts = $this->statieConfiguration->getOption('posts');
        $postWithRelatedItems = $posts[1];

        $relatedItems = $this->relatedItemsResolver->resolveForFile($postWithRelatedItems);

        $this->assertCount(3, $relatedItems);

        $relatedItem = $relatedItems[1];
        $this->assertSame('Statie 4: How to Create The Simplest Blog', $relatedItem['title']);
    }
}
