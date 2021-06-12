<?php

declare(strict_types=1);

namespace Symplify\Skipper\SkipVoter;

use Symplify\Skipper\Contract\SkipVoterInterface;
use Symplify\Skipper\Matcher\FileInfoMatcher;
use Symplify\Skipper\SkipCriteriaResolver\SkippedPathsResolver;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PathSkipVoter implements SkipVoterInterface
{
    public function __construct(
        private FileInfoMatcher $fileInfoMatcher,
        private SkippedPathsResolver $skippedPathsResolver
    ) {
    }

    /**
     * @param string|object $element
     */
    public function match(string | object $element): bool
    {
        return true;
    }

    /**
     * @param string|object $element
     */
    public function shouldSkip(string | object $element, SmartFileInfo $smartFileInfo): bool
    {
        $skippedPaths = $this->skippedPathsResolver->resolve();
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedPaths);
    }
}
