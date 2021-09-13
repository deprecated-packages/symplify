<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter\Tests\Latte;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\LineCommentMatcher;

final class LineCommentMatcherTest extends TestCase
{
    private LineCommentMatcher $lineCommentMatcher;

    protected function setUp(): void
    {
        $this->lineCommentMatcher = new LineCommentMatcher();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $docComment, int|null $expectedLine): void
    {
        $matchedLine = $this->lineCommentMatcher->matchLine($docComment);
        $this->assertSame($expectedLine, $matchedLine);
    }

    public function provideData(): Iterator
    {
        yield ['/* line 5 */', 5];
        yield ['/** line in latte file: 5000 */', 5000];

        yield ['some line', null];
        yield ['// line 5 */', null];
    }
}
