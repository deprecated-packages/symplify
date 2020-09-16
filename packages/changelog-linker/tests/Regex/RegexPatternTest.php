<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Regex;

use Iterator;
use Nette\Utils\Strings;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class RegexPatternTest extends TestCase
{
    /**
     * @dataProvider provideDataForLinkReference()
     * @param string[] $expectedMatches
     */
    public function testLinkReference(
        string $content,
        string $regexPattern,
        string $matchName,
        array $expectedMatches
    ): void {
        $matches = [];

        foreach (Strings::matchAll($content, $regexPattern) as $match) {
            $matches[] = $match[$matchName];
        }

        $this->assertSame($expectedMatches, $matches);
    }

    public function provideDataForLinkReference(): Iterator
    {
        yield ['[@Tomas]: http://', RegexPattern::LINK_REFERENCE_REGEX, 'reference', ['@Tomas']];
        yield ['Thanks to @Tomas', '#' . RegexPattern::USER . '#', 'reference', ['@Tomas']];
    }
}
