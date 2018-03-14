<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tests;

use Symplify\BetterReflectionDocBlock\CleanDocBlockFactory;
use Symplify\BetterReflectionDocBlock\DocBlockSerializerFactory;

/**
 * @covers \Symplify\BetterReflectionDocBlock\CleanFormatter
 * @covers \Symplify\BetterReflectionDocBlock\DocBlockSerializerFactory
 */
final class CleanFormatterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var CleanDocBlockFactory
     */
    private $cleanDocBlockFactory;

    protected function setUp(): void
    {
        $this->cleanDocBlockFactory = $this->container->get(CleanDocBlockFactory::class);
    }

    public function test(): void
    {
        $originalDocBlockContent = file_get_contents(__DIR__ . '/CleanFormatterSource/originalDocBlock.txt');

        $docBlock = $this->cleanDocBlockFactory->create($originalDocBlockContent);
        $docBlockSerializer = DocBlockSerializerFactory::createFromWhitespaceFixerConfigAndContent(
            $originalDocBlockContent,
            4,
            ' '
        );

        $resultDocBlock = $docBlockSerializer->getDocComment($docBlock);
        $this->assertSame(
            file_get_contents(__DIR__ . '/CleanFormatterSource/expectedDocBlock.txt'),
            trim($resultDocBlock)
        );
    }
}
