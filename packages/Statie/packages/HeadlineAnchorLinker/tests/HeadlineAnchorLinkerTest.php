<?php declare(strict_types=1);

namespace Symplify\Statie\HeadlineAnchorLinker\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\HeadlineAnchorLinker\HeadlineAnchorLinker;

final class HeadlineAnchorLinkerTest extends TestCase
{
    public function test(): void
    {
        $headlineAnchorLinker = new HeadlineAnchorLinker();

        $this->assertSame(
            '<h2 id="hey"><a href="#hey">Hey</a></h2>',
            $headlineAnchorLinker->processContent('<h2>Hey</h2>')
        );
        $this->assertSame(
            '<h3 id="hi-tom"><a href="#hi-tom">Hi Tom</a></h3>',
            $headlineAnchorLinker->processContent('<h3>Hi Tom</h3>')
        );
    }
}
