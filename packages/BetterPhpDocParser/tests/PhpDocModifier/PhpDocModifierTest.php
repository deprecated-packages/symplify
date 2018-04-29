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
    public function testRemoveTagByName(string $phpDocBefore, string $phpDocAfter, string $tagName): void
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFrom($phpDocBefore);
        $this->phpDocModifier->removeTagByName($phpDocInfo, $tagName);
        $this->assertSame($phpDocAfter, $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo));
    }

    public function provideDataForRemoveTagByName(): Iterator
    {
        yield [file_get_contents(__DIR__ . '/PhpDocModifierSource/before.txt'), '', 'var'];
        yield [file_get_contents(__DIR__ . '/PhpDocModifierSource/before.txt'), '', '@var'];
    }

    /**
     * @dataProvider provideDataForRemoveTagByNameAndContent()
     */
    public function testRemoveTagByNameAndContent(
        string $phpDocBefore,
        string $phpDocAfter,
        string $tagName,
        string $tagContent
    ): void {
        $phpDocInfo = $this->phpDocInfoFactory->createFrom($phpDocBefore);

        $this->phpDocModifier->removeTagByNameAndContent($phpDocInfo, $tagName, $tagContent);

        $this->assertSame($phpDocAfter, $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo));
    }

    public function provideDataForRemoveTagByNameAndContent(): Iterator
    {
//        yield [file_get_contents(__DIR__ . '/PhpDocModifierSource/before2.txt'), '', 'method', 'getThis()'];
        yield [
            file_get_contents(__DIR__ . '/PhpDocModifierSource/before3.txt'),
            file_get_contents(__DIR__ . '/PhpDocModifierSource/after3.txt'),
            'param',
            'paramName',
        ];

        yield [
            file_get_contents(__DIR__ . '/PhpDocModifierSource/before3.txt'),
            file_get_contents(__DIR__ . '/PhpDocModifierSource/after3.txt'),
            'param',
            '$paramName',
        ];
    }
}
