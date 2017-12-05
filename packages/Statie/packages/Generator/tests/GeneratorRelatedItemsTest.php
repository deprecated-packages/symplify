<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests;

use Symplify\Statie\Generator\RelatedItemsResolver;

final class GeneratorRelatedItemsTest extends AbstractGeneratorTest
{
    /**
     * @var RelatedItemsResolver
     */
    private $relatedPostsResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->relatedPostsResolver = $this->container->get(RelatedItemsResolver::class);
    }

    public function testRelatedItems(): void
    {
        $this->generator->run();
        $posts = $this->configuration->getOption('posts');
        $postWithRelatedItems = $posts[3];

        $relatedItems = $this->relatedPostsResolver->resolveForFile($postWithRelatedItems);

        $this->assertCount(3, $relatedItems);

        $relatedItem = $relatedItems[1];
        $this->assertSame('Statie 4: How to Create The Simplest Blog', $relatedItem['title']);
    }
}
