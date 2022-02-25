<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\MarkdownDiffer;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\RuleDocGenerator\Kernel\RuleDocGeneratorKernel;
use Symplify\RuleDocGenerator\MarkdownDiffer\MarkdownDiffer;

final class MarkdownDifferTest extends AbstractKernelTestCase
{
    private MarkdownDiffer $markdownDiffer;

    protected function setUp(): void
    {
        $this->bootKernel(RuleDocGeneratorKernel::class);

        $this->markdownDiffer = $this->getService(MarkdownDiffer::class);
    }

    public function test(): void
    {
        $currentDiff = $this->markdownDiffer->diff('old code', 'new code');
        $this->assertStringEqualsFile(__DIR__ . '/Fixture/expected_diff.txt', $currentDiff);
    }
}
