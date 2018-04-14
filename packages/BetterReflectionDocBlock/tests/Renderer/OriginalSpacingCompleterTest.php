<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tests\Renderer;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\BetterReflectionDocBlock\Renderer\OriginalSpacingCompleter;

final class OriginalSpacingCompleterTest extends TestCase
{
    /**
     * @var OriginalSpacingCompleter
     */
    private $originalSpacingCompleter;

    protected function setUp(): void
    {
        $this->originalSpacingCompleter = new OriginalSpacingCompleter();
    }

    /**
     * @dataProvider provideNewAndOriginalContents()
     */
    public function test(string $originalInFile, string $messedInFile): void
    {
        $original = file_get_contents($originalInFile);
        $messed = file_get_contents($messedInFile);

        $fixed = $this->originalSpacingCompleter->completeTagSpaces($messed, $original);
        $this->assertSame($original, trim($fixed));
    }

    public function provideNewAndOriginalContents(): Iterator
    {
        yield [
            __DIR__ . '/OriginalSpacingCompleterSource/original/original1.txt',
            __DIR__ . '/OriginalSpacingCompleterSource/messed/messed1.txt',
        ];
        yield [
            __DIR__ . '/OriginalSpacingCompleterSource/original/original2.txt',
            __DIR__ . '/OriginalSpacingCompleterSource/messed/messed2.txt',
        ];
    }
}
