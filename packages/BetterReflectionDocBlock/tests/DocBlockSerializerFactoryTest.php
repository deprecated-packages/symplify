<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tests;

use PhpCsFixer\WhitespacesFixerConfig;
use phpDocumentor\Reflection\DocBlock;
use PHPUnit\Framework\TestCase;
use Symplify\BetterReflectionDocBlock\DocBlockSerializerFactory;

final class DocBlockSerializerFactoryTest extends TestCase
{
    public function testNoSpaceOnEmptyLine(): void
    {
        $whitespaceFixerConfig = new WhitespacesFixerConfig();
        $docBlockSerializer = DocBlockSerializerFactory::createFromWhitespaceFixerConfigAndContent(
            $whitespaceFixerConfig,
            'someContent'
        );

        $docBlock = new DocBlock();

        $this->assertStringEqualsFile(
            __DIR__ . '/DocBlockSerializerFactorySource/Expected.php.inc',
            $docBlockSerializer->getDocComment($docBlock)
        );
    }
}
