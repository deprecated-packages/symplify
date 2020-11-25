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
     * @var mixed[]
     */
    private $only = [];

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

    public function __construct(
        ParameterProvider $parameterProvider,
        SkipRulesFactory $skipRulesFactory,
        ClassLikeExistenceChecker $classLikeExistenceChecker,
        FileInfoMatcher $fileInfoMatcher
    ) {
        $excludePaths = $parameterProvider->provideArrayParameter(Option::EXCLUDE_PATHS);

        $this->skipRules = $skipRulesFactory->create();

        $this->only = $parameterProvider->provideArrayParameter(Option::ONLY);
        $this->excludedPaths = $excludePaths;
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
        $this->fileInfoMatcher = $fileInfoMatcher;
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
        foreach ($this->excludedPaths as $excludedPath) {
            if ($this->fileInfoMatcher->doesFileMatchPattern($smartFileInfo, $excludedPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param object|string $class
     */
    private function shouldSkipClassAndFile($class, SmartFileInfo $smartFileInfo): bool
    {
        $doesMatchOnly = $this->doesMatchOnly($class, $smartFileInfo);
        if (is_bool($doesMatchOnly)) {
            return $doesMatchOnly;
        }

        return $this->doesMatchSkipped($class, $smartFileInfo);
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

        return $this->fileInfoMatcher->doesFileInfoMatchFilePattern($smartFileInfo, $skippedPaths);
    }

    /**
     * @param object|string $checker
     */
    private function doesMatchOnly($checker, SmartFileInfo $smartFileInfo): ?bool
    {
        foreach ($this->only as $onlyClass => $onlyFiles) {
            if (is_int($onlyClass)) {
                // solely class
                $onlyClass = $onlyFiles;
                $onlyFiles = null;
            }

            if (! is_a($checker, $onlyClass, true)) {
                continue;
            }

            if ($onlyFiles === null) {
                return true;
            }

            return ! $this->fileInfoMatcher->doesFileInfoMatchFilePattern($smartFileInfo, $onlyFiles);
        }

        return null;
    }

    /**
     * @param object|string $checker
     */
    private function doesMatchSkipped($checker, SmartFileInfo $smartFileInfo): bool
    {
        foreach ($this->skipRules->getSkippedClasses() as $skippedClass => $skippedFiles) {
            if (! is_a($checker, $skippedClass, true)) {
                continue;
            }

            // skip everywhere
            if (! is_array($skippedFiles)) {
                return true;
            }

            if ($this->fileInfoMatcher->doesFileInfoMatchFilePattern($smartFileInfo, $skippedFiles)) {
                return true;
            }
        }

        return false;
    }
}
