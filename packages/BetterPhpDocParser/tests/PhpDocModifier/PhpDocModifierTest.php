<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Tests\PhpDocModifier;

use Iterator;
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

    /**
     * @dataProvider provideDataForRemoveTagByName()
     */
    public function testRemoveTagByName(string $docFileBefore, string $docFileAfter, string $tagName)
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFrom(file_get_contents($docFileBefore));

        $this->phpDocModifier->removeTagByName($phpDocInfo, $tagName);

        $this->assertSame(
            file_get_contents($docFileAfter),
            $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo)
        );
    }

    public function provideDataForRemoveTagByName(): Iterator
    {
        yield [__DIR__ . '/PhpDocModifierSource/before.txt', __DIR__ . '/PhpDocModifierSource/after.txt', 'var'];
    }
}
