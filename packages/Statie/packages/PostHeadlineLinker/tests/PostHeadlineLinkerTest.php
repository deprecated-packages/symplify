<?php declare(strict_types=1);

namespace Symplify\Statie\PostHeadlineLinker\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\PostHeadlineLinker\PostHeadlineLinker;

final class PostHeadlineLinkerTest extends TestCase
{
    public function test(): void
    {
        $postHeadlineLinker = new PostHeadlineLinker(1, 3);

        $this->assertSame(
            '<h2 id="hey"><a href="#hey">Hey</a></h2>',
            $postHeadlineLinker->processContent('<h2>Hey</h2>')
        );
        $this->assertSame(
            '<h3 id="hi-tom"><a href="#hi-tom">Hi Tom</a></h3>',
            $postHeadlineLinker->processContent('<h3>Hi Tom</h3>')
        );
        $this->assertSame('<h4>Hey</h4>', $postHeadlineLinker->processContent('<h4>Hey</h4>'));
    }
}
