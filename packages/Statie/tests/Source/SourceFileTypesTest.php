<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\Source;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Source\SourceFileTypes;

final class SourceFileTypesTest extends TestCase
{
    public function test()
    {
        $this->assertSame('configuration', SourceFileTypes::CONFIGURATION);
        $this->assertSame('layouts', SourceFileTypes::GLOBAL_LATTE);
        $this->assertSame('posts', SourceFileTypes::POSTS);
        $this->assertSame('renderable', SourceFileTypes::RENDERABLE);
    }
}
