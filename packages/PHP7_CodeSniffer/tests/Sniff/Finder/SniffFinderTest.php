<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Finder;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\DI\ContainerFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;

final class SniffFinderTest extends TestCase
{
    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $this->sniffFinder = $container->getByType(SniffFinder::class);
    }

    public function testFindAllSniffs()
    {
        $allSniffs = $this->sniffFinder->findAllSniffClasses();
        $this->assertGreaterThan(250, $allSniffs);
    }

    public function testFindSniffsInDirectory()
    {
        $sniffs = $this->sniffFinder->findAllSniffClassesInDirectory(__DIR__ );
        $this->assertCount(12, $sniffs);
    }
}
