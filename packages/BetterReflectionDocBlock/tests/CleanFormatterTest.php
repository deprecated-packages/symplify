<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tests;

use Symplify\BetterReflectionDocBlock\CleanDocBlockFactory;
use Symplify\BetterReflectionDocBlock\DocBlockSerializerFactory;

/**
 * @see \Symplify\BetterReflectionDocBlock\CleanFormatter
 * @see \Symplify\BetterReflectionDocBlock\DocBlockSerializerFactory
 */
final class CleanFormatterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var CleanDocBlockFactory
     */
    private $cleanDocBlockFactory;

    /**
     * @var DocBlockSerializerFactory
     */
    private $docBlockSerializerFactory;

    protected function setUp(): void
    {
        $this->cleanDocBlockFactory = $this->container->get(CleanDocBlockFactory::class);
        $this->docBlockSerializerFactory = $this->container->get(DocBlockSerializerFactory::class);
    }

    public function test(): void
    {
        $originalDocBlockContent = file_get_contents(__DIR__ . '/CleanFormatterSource/originalDocBlock.txt');

        $docBlock = $this->cleanDocBlockFactory->create($originalDocBlockContent);
        $docBlockSerializer = $this->docBlockSerializerFactory->createFromWhitespaceFixerConfigAndContent(
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
