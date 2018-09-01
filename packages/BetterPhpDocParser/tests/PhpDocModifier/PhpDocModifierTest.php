<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Tests\PhpDocModifier;

use Iterator;
use Nette\Utils\FileSystem;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
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

    protected function setUp(): void
    {
        $this->phpDocInfoFactory = $this->container->get(PhpDocInfoFactory::class);
        $this->phpDocInfoPrinter = $this->container->get(PhpDocInfoPrinter::class);
    }

    /**
     * @dataProvider provideDataForRemoveTagByName()
     */
    public function testRemoveTagByName(string $phpDocBeforeFilePath, string $phpDocAfter, string $tagName): void
    {
        $phpDocBefore = FileSystem::read($phpDocBeforeFilePath);

        $phpDocInfo = $this->phpDocInfoFactory->createFrom($phpDocBefore);

        $phpDocInfo->removeTagByName($tagName);

        $this->assertSame($phpDocAfter, $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo));
    }

    public function provideDataForRemoveTagByName(): Iterator
    {
        yield [__DIR__ . '/PhpDocModifierSource/before.txt', '', 'var'];
        yield [__DIR__ . '/PhpDocModifierSource/before.txt', '', '@var'];
    }

    /**
     * @dataProvider provideDataForRemoveTagByNameAndContent()
     */
    public function testRemoveTagByNameAndContent(
        string $phpDocBeforeFilePath,
        string $phpDocAfter,
        string $tagName,
        string $tagContent
    ): void {
        $phpDocBefore = FileSystem::read($phpDocBeforeFilePath);

        $phpDocInfo = $this->phpDocInfoFactory->createFrom($phpDocBefore);

        $phpDocInfo->removeTagByNameAndContent($tagName, $tagContent);

        $this->assertSame($phpDocAfter, $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo));
    }

    public function provideDataForRemoveTagByNameAndContent(): Iterator
    {
        yield [__DIR__ . '/PhpDocModifierSource/before2.txt', '', 'method', 'getThis()'];
    }

    public function testRemoveTagByNameAndContentComplex(): void
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFrom(
            FileSystem::read(__DIR__ . '/PhpDocModifierSource/before4.txt')
        );

        $phpDocInfo->removeTagByNameAndContent('method', 'setName');
        $phpDocInfo->removeTagByNameAndContent('method', 'addItem');
        $phpDocInfo->removeTagByNameAndContent('method', 'setItems');
        $phpDocInfo->removeTagByNameAndContent('method', 'setEnabled');

        $this->assertStringEqualsFile(
            __DIR__ . '/PhpDocModifierSource/after4.txt',
            $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo)
        );
    }

    /**
     * @dataProvider provideDataForRemoveParamTagByParameter()
     */
    public function testRemoveParamTagByParameter(
        string $phpDocBeforeFilePath,
        string $phpDocAfterFilePath,
        string $parameterName
    ): void {
        $phpDocBefore = FileSystem::read($phpDocBeforeFilePath);
        $phpDocInfo = $this->phpDocInfoFactory->createFrom($phpDocBefore);

        $phpDocInfo->removeParamTagByParameter($parameterName);

        $this->assertStringEqualsFile(
            $phpDocAfterFilePath,
            $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo)
        );
    }

    public function provideDataForRemoveParamTagByParameter(): Iterator
    {
        yield [
            __DIR__ . '/PhpDocModifierSource/before3.txt',
            __DIR__ . '/PhpDocModifierSource/after3.txt',
            'paramName',
        ];

        yield [
            __DIR__ . '/PhpDocModifierSource/before3.txt',
            __DIR__ . '/PhpDocModifierSource/after3.txt',
            '$paramName',
        ];
    }
}
