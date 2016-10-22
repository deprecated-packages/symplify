<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Console;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_Sculpin\Console\Application;

final class ApplicationTest extends TestCase
{
    public function test()
    {
        $application = new Application();
        $this->assertSame('<info>Sculpin - Static Site Generator</info>', $application->getLongVersion());
    }
}
