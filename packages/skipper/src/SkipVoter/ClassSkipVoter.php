<?php

declare(strict_types=1);

namespace Symplify\Skipper\SkipVoter;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\Skipper\Contract\SkipVoterInterface;
use Symplify\Skipper\SkipCriteriaResolver\SkippedClassResolver;
use Symplify\Skipper\Skipper\OnlySkipper;
use Symplify\Skipper\Skipper\SkipSkipper;
use Symplify\Skipper\ValueObject\Option;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassSkipVoter implements SkipVoterInterface
{
    public function __construct(
        private ClassLikeExistenceChecker $classLikeExistenceChecker,
        private ParameterProvider $parameterProvider,
        private SkipSkipper $skipSkipper,
        private OnlySkipper $onlySkipper,
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

    public function shouldSkip(string | object $element, SmartFileInfo $smartFileInfo): bool
    {
        $only = $this->parameterProvider->provideArrayParameter(Option::ONLY);

        $doesMatchOnly = $this->onlySkipper->doesMatchOnly($element, $smartFileInfo, $only);
        if (is_bool($doesMatchOnly)) {
            return $doesMatchOnly;
        }

        $skippedClasses = $this->skippedClassResolver->resolve();
        return $this->skipSkipper->doesMatchSkip($element, $smartFileInfo, $skippedClasses);
    }
}
