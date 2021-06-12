<?php

declare(strict_types=1);

namespace Symplify\Skipper\SkipVoter;

use Symplify\Skipper\Contract\SkipVoterInterface;
use Symplify\Skipper\Matcher\FileInfoMatcher;
use Symplify\Skipper\SkipCriteriaResolver\SkippedClassAndCodesResolver;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * Matching class and code, e.g. App\Category\ArraySniff.SomeCode
 */
final class ClassAndCodeSkipVoter implements SkipVoterInterface
{
    public function __construct(
        private SkippedClassAndCodesResolver $skippedClassAndCodesResolver,
        private FileInfoMatcher $fileInfoMatcher
    ) {
    }

    public function match(string | object $element): bool
    {
        if (! is_string($element)) {
            return false;
        }

        return substr_count($element, '.') === 1;
    }

    public function shouldSkip(string | object $element, SmartFileInfo $smartFileInfo): bool
    {
        if (is_object($element)) {
            return false;
        }

        $skippedClassAndCodes = $this->skippedClassAndCodesResolver->resolve();
        if (! array_key_exists($element, $skippedClassAndCodes)) {
            return false;
        }

        // skip regardless the path
        $skippedPaths = $skippedClassAndCodes[$element];
        if ($skippedPaths === null) {
            return true;
        }

        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedPaths);
    }
}
