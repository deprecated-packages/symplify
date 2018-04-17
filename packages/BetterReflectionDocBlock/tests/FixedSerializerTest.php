<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tests;

use phpDocumentor\Reflection\DocBlock;
use Symplify\BetterReflectionDocBlock\CleanDocBlockFactory;
use Symplify\BetterReflectionDocBlock\CleanFormatter;
use Symplify\BetterReflectionDocBlock\FixedSerializer;

/**
 * @todo replace by @see \Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfoPrinter, already covered
 */
final class FixedSerializerTest extends AbstractContainerAwareTestCase
{
    /**
     * @var CleanDocBlockFactory
     */
    private $cleanDocBlockFactory;

    protected function setUp(): void
    {
        $this->cleanDocBlockFactory = $this->container->get(CleanDocBlockFactory::class);
    }

    public function testNoSpaceOnEmptyLine(): void
    {
        $docBlockSerializer = new FixedSerializer(4, ' ', false, null, new CleanFormatter('someContent'));

        $docBlock = new DocBlock();

        $this->assertStringEqualsFile(
            __DIR__ . '/FixedSerializerSource/Expected.php.inc',
            $docBlockSerializer->getDocComment($docBlock)
        );
    }

    public function testKeepSpaceBetweentTagsAsBefore(): void
    {
        $docBlockContent = file_get_contents(__DIR__ . '/FixedSerializerSource/originalDocBlock.txt');
        $docBlock = $this->cleanDocBlockFactory->create($docBlockContent);

        $cleanFormatter = new CleanFormatter($docBlockContent);
        $docBlockSerializer = new FixedSerializer(4, ' ', false, null, $cleanFormatter);
        $docBlockSerializer->setOriginalContent($docBlockContent);

        $expectedDocBlockContent = file_get_contents(__DIR__ . '/FixedSerializerSource/expectedDocBlock.txt');
        $this->assertSame($expectedDocBlockContent, $docBlockSerializer->getDocComment($docBlock));
    }
}
