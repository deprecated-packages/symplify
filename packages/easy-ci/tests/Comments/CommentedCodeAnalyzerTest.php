<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Comments;

use Symplify\EasyCI\Comments\CommentedCodeAnalyzer;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class CommentedCodeAnalyzerTest extends AbstractKernelTestCase
{
    private CommentedCodeAnalyzer $commentedCodeAnalyzer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->commentedCodeAnalyzer = $this->getService(CommentedCodeAnalyzer::class);
    }

    public function test(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixture/some_commented_code.php.inc');
        $commentedLines = $this->commentedCodeAnalyzer->process($fileInfo, 4);
        $this->assertSame([], $commentedLines);

        $commentedLines = $this->commentedCodeAnalyzer->process($fileInfo, 2);
        $this->assertSame([5], $commentedLines);
    }
}
