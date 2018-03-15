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

    public function test(): void
    {
        $content = '/** @var Type */';

        $phpDocInfo = $this->phpDocInfoFactory->createFrom($content);

        $this->assertSame($content, (string) $phpDocInfo, 'Rendered parser doc block is the same as original');
    }

    private function isSingleLineDoc(string $content): bool
    {
        return substr_count($content, PHP_EOL) <= 1;
    }
}
