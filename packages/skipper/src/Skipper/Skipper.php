<?php

declare(strict_types=1);

namespace Symplify\Skipper\Skipper;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Skipper\FileSystem\PathNormalizer;
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
     * @var PathNormalizer
     */
    private $pathNormalizer;

    public function __construct(
        ParameterProvider $parameterProvider,
        SkipRulesFactory $skipRulesFactory,
        PathNormalizer $pathNormalizer
    ) {
        $skip = $parameterProvider->provideArrayParameter(Option::SKIP);
        $only = $parameterProvider->provideArrayParameter(Option::ONLY);

        $excludePaths = $parameterProvider->provideArrayParameter(Option::EXCLUDE_PATHS);

        $this->skipRules = $skipRulesFactory->createFromSkipParameter($skip);

        $this->only = $only;
        $this->excludedPaths = $excludePaths;
        $this->pathNormalizer = $pathNormalizer;
    }

    public function shouldSkipCodeAndFile(string $code, SmartFileInfo $smartFileInfo): bool
    {
        return $this->shouldSkipMatchingRuleAndFile($this->skipRules->getSkippedCodes(), $code, $smartFileInfo);
    }

    public function shouldSkipMessageAndFile(string $message, SmartFileInfo $smartFileInfo): bool
    {
        return $this->shouldSkipMatchingRuleAndFile($this->skipRules->getSkippedMessages(), $message, $smartFileInfo);
    }

    /**
     * @param object|string $class
     */
    public function shouldSkipClassAndFile($class, SmartFileInfo $smartFileInfo): bool
    {
        $doesMatchOnly = $this->doesMatchOnly($class, $smartFileInfo);
        if (is_bool($doesMatchOnly)) {
            return $doesMatchOnly;
        }

        return $this->doesMatchSkipped($class, $smartFileInfo);
    }

    public function shouldSkipFileInfo(SmartFileInfo $smartFileInfo): bool
    {
        foreach ($this->excludedPaths as $excludedPath) {
            if ($this->doesFileMatchPattern($smartFileInfo, $excludedPath)) {
                return true;
            }
        }

        return false;
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

        return $this->doesFileMatchSkippedFiles($smartFileInfo, $skippedPaths);
    }

    /**
     * @param object|string $checker
     */
    private function doesMatchOnly($checker, SmartFileInfo $smartFileInfo): ?bool
    {
        foreach ($this->only as $onlyClass => $onlyFiles) {
            if (! is_a($checker, $onlyClass, true)) {
                continue;
            }

            foreach ($onlyFiles as $onlyFile) {
                if ($this->doesFileMatchPattern($smartFileInfo, $onlyFile)) {
                    return false;
                }
            }

            return true;
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

            if ($this->doesFileMatchSkippedFiles($smartFileInfo, $skippedFiles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Supports both relative and absolute $file path.
     * They differ for PHP-CS-Fixer and PHP_CodeSniffer.
     */
    private function doesFileMatchPattern(SmartFileInfo $smartFileInfo, string $ignoredPath): bool
    {
        // in ecs.php, the path can be absolute
        if ($smartFileInfo->getRealPath() === $ignoredPath) {
            return true;
        }

        $ignoredPath = $this->pathNormalizer->normalizeForFnmatch($ignoredPath);

        return $smartFileInfo->endsWith($ignoredPath) || $smartFileInfo->doesFnmatch($ignoredPath);
    }

    /**
     * @param string[] $skippedFiles
     */
    private function doesFileMatchSkippedFiles(SmartFileInfo $smartFileInfo, array $skippedFiles): bool
    {
        foreach ($skippedFiles as $skippedFile) {
            if ($this->doesFileMatchPattern($smartFileInfo, $skippedFile)) {
                return true;
            }
        }

        return false;
    }
}
