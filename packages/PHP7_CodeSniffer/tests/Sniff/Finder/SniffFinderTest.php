<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Finder;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\DI\ContainerFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;

final class SniffFinderTest extends TestCase
{
    public function test()
    {
        $container = (new ContainerFactory())->create();
        $sniffFinder = $container->getByType(SniffFinder::class);
        $this->assertGreaterThan(250, $sniffFinder->findAllSniffClasses());
    }
}
