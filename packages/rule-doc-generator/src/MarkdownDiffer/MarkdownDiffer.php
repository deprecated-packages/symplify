<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\MarkdownDiffer;

use Nette\Utils\Strings;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\RuleDocGenerator\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;

/**
 * @see \Symplify\RuleDocGenerator\Tests\MarkdownDiffer\MarkdownDifferTest
 */
final class MarkdownDiffer
{
    /**
     * @var string
     * @see https://regex101.com/r/LE9Xwo/1
     */
    private const METADATA_REGEX = '#^(.*\n){1}#';

    /**
     * @var string
     * @see https://regex101.com/r/yf7u2L/1
     */
    private const SPACE_AND_NEWLINE_REGEX = '#( ){1,}\n#';

    private readonly Differ $differ;

    public function __construct(
    ) {
        $completeUnifiedDiffOutputBuilderFactory = new CompleteUnifiedDiffOutputBuilderFactory(
            new PrivatesAccessor(),
        );
        $unifiedDiffOutputBuilder = $completeUnifiedDiffOutputBuilderFactory->create();

        $this->differ = new Differ($unifiedDiffOutputBuilder);
    }

    public function diff(string $old, string $new): string
    {
        if ($old === $new) {
            return '';
        }

        $diff = $this->differ->diff($old, $new);

        $diff = $this->clearUnifiedDiffOutputFirstLine($diff);
        $diff = $this->removeTrailingWhitespaces($diff);

        return $this->warpToDiffCode($diff);
    }

    /**
     * Removes UnifiedDiffOutputBuilder generated pre-spaces " \n" => "\n"
     */
    private function removeTrailingWhitespaces(string $diff): string
    {
        $diff = Strings::replace($diff, self::SPACE_AND_NEWLINE_REGEX, PHP_EOL);

        return rtrim($diff);
    }

    private function warpToDiffCode(string $content): string
    {
        return '```diff' . PHP_EOL . $content . PHP_EOL . '```' . PHP_EOL;
    }

    private function clearUnifiedDiffOutputFirstLine(string $diff): string
    {
        return Strings::replace($diff, self::METADATA_REGEX, '');
    }
}
