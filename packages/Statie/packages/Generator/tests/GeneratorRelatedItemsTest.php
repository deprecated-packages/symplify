<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Tests;

use Symplify\Statie\RelatedPosts\RelatedPostsResolver;

final class GeneratorRelatedItemsTest extends AbstractGeneratorTest
{
    /**
     * @var RelatedPostsResolver
     */
    private $relatedPostsResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->relatedPostsResolver = $this->container->get(RelatedPostsResolver::class);
    }

    public function testRelatedItems(): void
    {
        $this->generator->run();
        $posts = $this->configuration->getOption('posts');
        $postWithRelatedItems = $posts[3];

        $relatedItems = $this->relatedPostsResolver->resolveForPost($postWithRelatedItems);

        $this->assertCount(3, $relatedItems);

        $relatedItem = $relatedItems[1];
        $this->assertSame('Statie 4: How to Create The Simplest Blog', $relatedItem['title']);
    }
}
