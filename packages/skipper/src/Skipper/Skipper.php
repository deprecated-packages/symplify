<?php

declare(strict_types=1);

namespace Symplify\Skipper\Skipper;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\Skipper\Matcher\FileInfoMatcher;
use Symplify\Skipper\ValueObject\Option;
use Symplify\Skipper\ValueObject\SkipRules;
use Symplify\Skipper\ValueObjectFactory\SkipRulesFactory;
use Symplify\SmartFileSystem\SmartFileInfo;

final class Skipper
{
    /**
     * @var string[]
     */
    private $excludedPaths = [];

    /**
     * @var SkipRules
     */
    private $skipRules;

    /**
     * @var ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;

    /**
     * @var FileInfoMatcher
     */
    private $fileInfoMatcher;

    /**
     * @var OnlySkipper
     */
    private $onlySkipper;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var SkipSkipper
     */
    private $skipSkipper;

    public function __construct(
        ParameterProvider $parameterProvider,
        SkipRulesFactory $skipRulesFactory,
        ClassLikeExistenceChecker $classLikeExistenceChecker,
        FileInfoMatcher $fileInfoMatcher,
        OnlySkipper $onlySkipper,
        SkipSkipper $skipSkipper
    ) {
        $excludePaths = $parameterProvider->provideArrayParameter(Option::EXCLUDE_PATHS);

        $this->skipRules = $skipRulesFactory->create();

        $this->excludedPaths = $excludePaths;
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
        $this->fileInfoMatcher = $fileInfoMatcher;
        $this->onlySkipper = $onlySkipper;
        $this->parameterProvider = $parameterProvider;
        $this->skipSkipper = $skipSkipper;
    }

    public function shouldSkipElementAndFileInfo($element, SmartFileInfo $fileInfo): bool
    {
        if (is_object($element) || $this->classLikeExistenceChecker->doesClassLikeExist($element)) {
            return $this->shouldSkipClassAndFile($element, $fileInfo);
        }

        return false;
    }

    public function shouldSkipCodeAndFile(string $code, SmartFileInfo $smartFileInfo): bool
    {
        return $this->shouldSkipMatchingRuleAndFile($this->skipRules->getSkippedCodes(), $code, $smartFileInfo);
    }

    public function shouldSkipMessageAndFile(string $message, SmartFileInfo $smartFileInfo): bool
    {
        return $this->shouldSkipMatchingRuleAndFile($this->skipRules->getSkippedMessages(), $message, $smartFileInfo);
    }

    public function shouldSkipFileInfo(SmartFileInfo $smartFileInfo): bool
    {
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $this->excludedPaths);
    }

    /**
     * @param object|string $class
     */
    private function shouldSkipClassAndFile($class, SmartFileInfo $smartFileInfo): bool
    {
        $only = $this->parameterProvider->provideArrayParameter(Option::ONLY);

        $doesMatchOnly = $this->onlySkipper->doesMatchOnly($class, $smartFileInfo, $only);
        if (is_bool($doesMatchOnly)) {
            return $doesMatchOnly;
        }

        return $this->skipSkipper->doesMatchSkip($class, $smartFileInfo, $this->skipRules->getSkippedClasses());
    }

    private function shouldSkipMatchingRuleAndFile(array $skipped, string $key, SmartFileInfo $smartFileInfo): bool
    {
        if (! array_key_exists($key, $skipped)) {
            return false;
        }

        // skip regardless the path
        $skippedPaths = $skipped[$key];
        if ($skippedPaths === null) {
            return true;
        }

        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedPaths);
    }
}
