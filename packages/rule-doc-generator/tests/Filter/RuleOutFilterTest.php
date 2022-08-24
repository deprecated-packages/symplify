<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\Filter;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\RuleDocGenerator\DirectoryToMarkdownPrinter;
use Symplify\RuleDocGenerator\Kernel\RuleDocGeneratorKernel;

final class RuleOutFilterTest extends AbstractKernelTestCase
{
    private DirectoryToMarkdownPrinter $directoryToMarkdownPrinter;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(RuleDocGeneratorKernel::class, [
            __DIR__ . '/config/config_with_out_filter.php',
        ]);

        $this->directoryToMarkdownPrinter = $this->getService(DirectoryToMarkdownPrinter::class);
    }

    public function test(): void
    {
        $fileContent = $this->directoryToMarkdownPrinter->print(__DIR__, [__DIR__ . '/Fixture'], false);

        // the Fixture contains 2 rules, but the printed markdown contains only 1
        $this->assertStringEqualsFile(__DIR__ . '/Expected/expected_markdown.md', $fileContent);
    }
}
