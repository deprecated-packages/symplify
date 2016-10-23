<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Source;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_Sculpin\Source\SourceFileTypes;

final class SourceFileTypesTest extends TestCase
{
    public function test()
    {
        $this->assertSame('configuration', SourceFileTypes::CONFIGURATION);
        $this->assertSame('layouts', SourceFileTypes::LAYOUTS);
        $this->assertSame('posts', SourceFileTypes::POSTS);
        $this->assertSame('renderable', SourceFileTypes::RENDERABLE);
    }
}
