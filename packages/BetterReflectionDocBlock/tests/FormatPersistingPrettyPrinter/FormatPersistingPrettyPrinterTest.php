<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tests\FormatPersistingPrettyPrinter;

use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfoFactory;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocParser;
use Symplify\BetterReflectionDocBlock\Tests\AbstractContainerAwareTestCase;

final class FormatPersistingPrettyPrinterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PhpDocParser
     */
    private $phpDocParser;

    /**
     * @var PhpDocInfoFactory
     */
    private $phpDocInfoFactory;

    protected function setUp(): void
    {
        $this->phpDocParser = $this->container->get(PhpDocParser::class);
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

    public function providePhpDocs(): \Iterator
    {
        yield [
            'single line with type',
            '/** @var Type */'
        ];
    }
}
