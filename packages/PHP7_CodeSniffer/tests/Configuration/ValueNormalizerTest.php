<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Configuration\ValueNormalizer;

final class ValueNormalizerTest extends TestCase
{
    public function testNormalizeCommaSeparatedValues()
    {
        $valueNormalizer = new ValueNormalizer();
        $normalizedValues = $valueNormalizer->normalizeCommaSeparatedValues([
           'one,two'
        ]);

        $this->assertSame(['one', 'two'], $normalizedValues);
    }
}
