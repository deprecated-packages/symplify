<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tests\PhpDocParser;

use Iterator;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfoFactory;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfoRenderer;
use Symplify\BetterReflectionDocBlock\Tests\AbstractContainerAwareTestCase;

final class PhpDocInfoFactoryTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PhpDocInfoFactory
     */
    private $phpDocInfoFactory;

    /**
     * @var PhpDocInfoRenderer
     */
    private $phpDocInfoRenderer;

    protected function setUp(): void
    {
        $this->phpDocInfoFactory = $this->container->get(PhpDocInfoFactory::class);
        $this->phpDocInfoRenderer = $this->container->get(PhpDocInfoRenderer::class);
    }

    /**
     * @dataProvider provideDocFiles()
     */
    public function testSingleLine(string $docFile): void
    {
        $docComment = file_get_contents($docFile);

        $phpDocInfo = $this->phpDocInfoFactory->createFrom($docComment);
        $this->assertSame($docComment, $this->phpDocInfoRenderer->render($phpDocInfo));
    }

    public function provideDocFiles(): Iterator
    {
        yield [__DIR__ . '/PhpDocInfoFactorySource/doc.txt'];
        yield [__DIR__ . '/PhpDocInfoFactorySource/doc2.txt'];
    }
}
