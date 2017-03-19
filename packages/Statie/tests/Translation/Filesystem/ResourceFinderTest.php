<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Translation\Filesystem;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Translation\Filesystem\ResourceFinder;

final class ResourceFinderTest extends TestCase
{
    /**
     * @var ResourceFinder
     */
    private $resourceFinder;

    protected function setUp(): void
    {
        $this->resourceFinder = new ResourceFinder;
    }

    public function test(): void
    {
        $resources = $this->resourceFinder->findInDirectory(__DIR__ . '/ResourceFinderSource');
        $this->assertCount(1, $resources);

        /** @todo consider hydration to object */
        $resource = $resources[0];
        $this->assertSame('neon', $resource['format']);
        $this->assertSame('en', $resource['locale']);
        $this->assertSame('layout', $resource['domain']);
        $this->assertSame(__DIR__ . '/ResourceFinderSource/layout.en.neon', $resource['pathname']);
    }
}
