<?php

declare(strict_types=1);

namespace Symplify\Skipper\SkipVoter;

use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\Skipper\Contract\SkipVoterInterface;
use Symplify\Skipper\SkipCriteriaResolver\SkippedClassResolver;
use Symplify\Skipper\Skipper\SkipSkipper;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassSkipVoter implements SkipVoterInterface
{
    public function __construct(
        private ClassLikeExistenceChecker $classLikeExistenceChecker,
        private SkipSkipper $skipSkipper,
        private SkippedClassResolver $skippedClassResolver
    ) {
    }

    public function match(string | object $element): bool
    {
        if (is_object($element)) {
            return true;
        }

        return $this->classLikeExistenceChecker->doesClassLikeExist($element);
    }

    public function shouldSkip(string | object $element, SmartFileInfo | string $file): bool
    {
        $skippedClasses = $this->skippedClassResolver->resolve();
        return $this->skipSkipper->doesMatchSkip($element, $file, $skippedClasses);
    }
}
