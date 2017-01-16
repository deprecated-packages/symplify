<?php

declare(strict_types=1);

namespace Symplify\MultiCodingStandard\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use Symplify\MultiCodingStandard\Configuration\MultiCsFileLoader;

final class MultiCsFileLoaderTest extends TestCase
{
    public function testLoad()
    {
        $multiCsFileLoader = new MultiCsFileLoader(__DIR__.'/multi-cs-key-value.json');

        $loadedFile = $multiCsFileLoader->load();
        $this->assertSame([
           'key' => 'value',
        ], $loadedFile);
    }
}
