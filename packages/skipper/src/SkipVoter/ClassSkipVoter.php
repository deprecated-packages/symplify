<?php

declare(strict_types=1);

namespace Symplify\Skipper\SkipVoter;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\Skipper\Contract\SkipVoterInterface;
use Symplify\Skipper\Skipper\OnlySkipper;
use Symplify\Skipper\Skipper\SkipSkipper;
use Symplify\Skipper\ValueObject\Option;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassSkipVoter implements SkipVoterInterface
{
    /**
     * @var ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var SkipSkipper
     */
    private $skipSkipper;

    /**
     * @var OnlySkipper
     */
    private $onlySkipper;

    /**
     * @var array<string, string[]|null>
     */
    private $skippedClasses = [];

    public function __construct(
        ClassLikeExistenceChecker $classLikeExistenceChecker,
        ParameterProvider $parameterProvider,
        SkipSkipper $skipSkipper,
        OnlySkipper $onlySkipper
    ) {
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
        $this->parameterProvider = $parameterProvider;

        $this->skipSkipper = $skipSkipper;
        $this->onlySkipper = $onlySkipper;
    }

    /**
     * @param string|object $element
     */
    public function match($element): bool
    {
        if (is_object($element)) {
            return true;
        }

        return $this->classLikeExistenceChecker->doesClassLikeExist($element);
    }

    /**
     * @param string|object $element
     */
    public function shouldSkip($element, SmartFileInfo $smartFileInfo): bool
    {
        $only = $this->parameterProvider->provideArrayParameter(Option::ONLY);

        $doesMatchOnly = $this->onlySkipper->doesMatchOnly($element, $smartFileInfo, $only);
        if (is_bool($doesMatchOnly)) {
            return $doesMatchOnly;
        }

        $skippedClasses = $this->resolveSkippedClasses();
        return $this->skipSkipper->doesMatchSkip($element, $smartFileInfo, $skippedClasses);
    }

    /**
     * @return array<string, string[]|null>
     */
    private function resolveSkippedClasses(): array
    {
        if ($this->skippedClasses !== []) {
            return $this->skippedClasses;
        }

        $skip = $this->parameterProvider->provideArrayParameter(Option::SKIP);

        foreach ($skip as $key => $value) {
            // e.g. [SomeClass::class] → shift values to [SomeClass::class => null]
            if (is_int($key)) {
                $key = $value;
                $value = null;
            }

            if (! is_string($key)) {
                continue;
            }

            if (! $this->classLikeExistenceChecker->doesClassLikeExist($key)) {
                continue;
            }

            $this->skippedClasses[$key] = $value;
        }

        return $this->skippedClasses;
    }
}
