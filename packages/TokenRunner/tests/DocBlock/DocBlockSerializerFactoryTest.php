<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\DocBlock;

use PhpCsFixer\WhitespacesFixerConfig;
use phpDocumentor\Reflection\DocBlock;
use PHPUnit\Framework\TestCase;
use Symplify\TokenRunner\DocBlock\DocBlockSerializerFactory;

final class DocBlockSerializerFactoryTest extends TestCase
{
    public function testNoSpaceOnEmptyLine(): void
    {
        $whitespaceFixerConfig = new WhitespacesFixerConfig();
        $docBlockSerializer = DocBlockSerializerFactory::createFromWhitespaceFixerConfig($whitespaceFixerConfig);

        $docBlock = new DocBlock();

        $this->assertSame(
            file_get_contents(__DIR__ . '/DocBlockSerializerFactorySource/Expected.php.inc'),
            $docBlockSerializer->getDocComment($docBlock)
        );
    }
}
