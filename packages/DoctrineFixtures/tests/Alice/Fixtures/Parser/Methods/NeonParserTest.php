<?php declare(strict_types=1);

namespace Symplify\DoctrineFixtures\Tests\Alice\Fixtures\Parser\Methods;

use PHPUnit\Framework\TestCase;
use Symplify\DoctrineFixtures\Alice\Fixtures\Parser\Methods\NeonParser;
use Symplify\DoctrineFixtures\Tests\Entity\Product;
use Symplify\DoctrineFixtures\Tests\Entity\User;

final class NeonParserTest extends TestCase
{
    /**
     * @var NeonParser
     */
    private $neonParser;

    protected function setUp(): void
    {
        $this->neonParser = new NeonParser;
    }

    public function testCanParse(): void
    {
        $this->assertTrue($this->neonParser->canParse('file.neon'));
        $this->assertFalse($this->neonParser->canParse('file.yaml'));
    }

    public function testParse(): void
    {
        $entities = $this->neonParser->parse(__DIR__ . '/NeonParserSource/products.neon');
        $this->assertArrayHasKey(Product::class, $entities);
        $this->assertArrayHasKey('product{1..5}', $entities[Product::class]);
    }

    public function testInclude(): void
    {
        $entities = $this->neonParser->parse(__DIR__ . '/NeonParserSource/include.neon');
        $this->assertArrayHasKey(User::class, $entities);

        $entities = $this->neonParser->parse(__DIR__ . '/NeonParserSource/includes.neon');
        $this->assertArrayHasKey(User::class, $entities);
    }
}
