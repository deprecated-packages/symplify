<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tests;

use phpDocumentor\Reflection\DocBlock;
use PHPUnit\Framework\TestCase;
use Symplify\BetterReflectionDocBlock\CleanFormatter;
use Symplify\BetterReflectionDocBlock\FixedSerializer;

final class FixedSerializerTest extends TestCase
{
    public function testNoSpaceOnEmptyLine(): void
    {
        $docBlockSerializer = new FixedSerializer(4, ' ', false, null, new CleanFormatter('someContent'));

        $docBlock = new DocBlock();

        $this->assertStringEqualsFile(
            __DIR__ . '/FixedSerializerSource/Expected.php.inc',
            $docBlockSerializer->getDocComment($docBlock)
        );
    }
}
