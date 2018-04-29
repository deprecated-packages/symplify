<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Tests\PhpDocModifier;

use Symplify\BetterPhpDocParser\PhpDocModifier;
use Symplify\BetterPhpDocParser\PhpDocParser\PhpDocInfoFactory;
use Symplify\BetterPhpDocParser\Printer\PhpDocInfoPrinter;
use Symplify\BetterPhpDocParser\Tests\AbstractContainerAwareTestCase;

final class PhpDocModifierTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PhpDocInfoFactory
     */
    private $phpDocInfoFactory;

    /**
     * @var PhpDocInfoPrinter
     */
    private $phpDocInfoPrinter;

    /**
     * @var PhpDocModifier
     */
    private $phpDocModifier;

    protected function setUp(): void
    {
        $this->phpDocInfoFactory = $this->container->get(PhpDocInfoFactory::class);
        $this->phpDocInfoPrinter = $this->container->get(PhpDocInfoPrinter::class);
        $this->phpDocModifier = $this->container->get(PhpDocModifier::class);
    }

    public function test()
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFrom(__DIR__ . '/PhpDocModifierSource/before.txt');
        $this->phpDocModifier->removeTagByName($phpDocInfo, 'var');

        $this->assertSame(
            file_get_contents(__DIR__ . '/PhpDocModifierSource/after.txt'),
            $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo)
        );
    }
}
