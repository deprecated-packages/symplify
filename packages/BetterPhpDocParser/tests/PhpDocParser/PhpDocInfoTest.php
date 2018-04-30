<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Tests\PhpDocParser;

use Symplify\BetterPhpDocParser\PhpDocParser\PhpDocInfoFactory;
use Symplify\BetterPhpDocParser\Tests\AbstractContainerAwareTestCase;

final class PhpDocInfoTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PhpDocInfoFactory
     */
    private $phpDocInfoFactory;

    protected function setUp(): void
    {
        $this->phpDocInfoFactory = $this->container->get(PhpDocInfoFactory::class);
    }

    public function testPrintFormatPreserving(): void
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFrom(file_get_contents(__DIR__ . '/PhpDocInfoSource/doc.txt'));

        $this->assertTrue($phpDocInfo->hasTag('param'));
        $this->assertTrue($phpDocInfo->hasTag('@throw'));

        $this->assertFalse($phpDocInfo->hasTag('random'));
    }
}
