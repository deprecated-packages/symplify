<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\Strings;

use PHPUnit\Framework\TestCase;
use Symplify\GitWrapper\Strings\GitStrings;

final class GitStringsTest extends TestCase
{
    public function testParseRepositoryName(): void
    {
        $nameGit = GitStrings::parseRepositoryName('git@github.com:symplify/git-wrapper.git');
        $this->assertSame($nameGit, 'git-wrapper');

        $nameHttps = GitStrings::parseRepositoryName('https://github.com/symplify/git-wrapper.git');
        $this->assertSame($nameHttps, 'git-wrapper');
    }
}
