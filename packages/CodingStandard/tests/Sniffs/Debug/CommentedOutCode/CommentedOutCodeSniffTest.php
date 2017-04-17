<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Debug\CommentedOutCode;

use Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class CommentedOutCodeSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(CommentedOutCodeSniff::class, __DIR__);
    }
}
