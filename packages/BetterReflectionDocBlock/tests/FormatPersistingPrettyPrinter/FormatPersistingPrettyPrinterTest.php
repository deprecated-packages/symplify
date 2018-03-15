<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tests\FormatPersistingPrettyPrinter;

use Iterator;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfoFactory;
use Symplify\BetterReflectionDocBlock\Tests\AbstractContainerAwareTestCase;

/**
 * @cover \Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocParser
 */
final class FormatPersistingPrettyPrinterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PhpDocInfoFactory
     */
    private $phpDocInfoFactory;

    protected function setUp(): void
    {
        $this->phpDocInfoFactory = $this->container->get(PhpDocInfoFactory::class);
    }

    /**
     * @dataProvider providePhpDocs()
     */
    public function test(string $description, string $content): void
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFrom($content);

        $this->assertSame($content, (string) $phpDocInfo, $description);
    }

    public function providePhpDocs(): Iterator
    {
        yield [
            'single line with type',
            '/** @var Type */',
        ];

        yield [
            'fixed serializer test case',
            file_get_contents(__DIR__ . '/../FixedSerializerSource/originalDocBlock.txt'),
        ];

        yield [
            'clean formatter test case',
            file_get_contents(__DIR__ . '/../CleanFormatterSource/originalDocBlock.txt'),
        ];
    }
}
