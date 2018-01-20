<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tests;

use PhpCsFixer\WhitespacesFixerConfig;
use PHPUnit\Framework\TestCase;
use Symplify\BetterReflectionDocBlock\CleanDocBlockFactory;
use Symplify\BetterReflectionDocBlock\DocBlockSerializerFactory;

final class CleanFormatterTest extends TestCase
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
        $this->cleanDocBlockFactory = new CleanDocBlockFactory();
        $this->docBlockSerializerFactory = new DocBlockSerializerFactory();
    }

    public function test(): void
    {
        $originalDocBlockContent = file_get_contents(__DIR__ . '/CleanFormatterSource/originalDocBlock.txt');

        $docBlock = $this->cleanDocBlockFactory->create($originalDocBlockContent);
        $docBlockSerializer = $this->docBlockSerializerFactory->createFromWhitespaceFixerConfigAndContent(
            new WhitespacesFixerConfig(),
            $originalDocBlockContent
        );

        $resultDocBlock = $docBlockSerializer->getDocComment($docBlock);
        $this->assertSame(
            file_get_contents(__DIR__ . '/CleanFormatterSource/expectedDocBlock.txt'),
            trim($resultDocBlock)
        );
    }
}
