<?php

declare(strict_types=1);

namespace Symplify\MarkdownDiff\Diff\Output;

use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

/**
 * Creates @see UnifiedDiffOutputBuilder with "$contextLines = 1000;"
 */
final class CompleteUnifiedDiffOutputBuilderFactory
{
    public function __construct(
        private PrivatesAccessor $privatesAccessor
    ) {
    }

    /**
     * @api
     */
    public function create(): UnifiedDiffOutputBuilder
    {
        $unifiedDiffOutputBuilder = new UnifiedDiffOutputBuilder('');
        $this->privatesAccessor->setPrivateProperty($unifiedDiffOutputBuilder, 'contextLines', 10000);
        return $unifiedDiffOutputBuilder;
    }
}
