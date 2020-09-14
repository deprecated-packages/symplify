<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Markdown;

use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\AbstractPHPFormatter;

/**
 * @see \Symplify\EasyCodingStandard\SnippetFormatter\Tests\Markdown\MarkdownPHPCodeFormatterTest
 */
final class MarkdownPHPCodeFormatter extends AbstractPHPFormatter
{
    /**
     * @see https://regex101.com/r/4YUIu1/4
     * @var string
     */
    private const PHP_CODE_SNIPPET = '#(?<opening>\`\`\`php\s+)(?<content>[^\`\`\`|^\-\-\-\-\-]+\n)(?<closing>(\s+)?\`\`\`)#ms';

    public function provideRegex(): string
    {
        return self::PHP_CODE_SNIPPET;
    }
}
