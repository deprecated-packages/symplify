<?php

declare(strict_types=1);

namespace Symplify\Skipper\Skipper;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Skipper\Contract\SkipVoterInterface;
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
     * @var FileInfoMatcher
     */
    private $fileInfoMatcher;

    /**
     * @var SkipVoterInterface[]
     */
    private $skipVoters = [];

    /**
     * @param SkipVoterInterface[] $skipVoters
     */
    public function __construct(
        ParameterProvider $parameterProvider,
        SkipRulesFactory $skipRulesFactory,
        FileInfoMatcher $fileInfoMatcher,
        array $skipVoters
    ) {
        $excludePaths = $parameterProvider->provideArrayParameter(Option::EXCLUDE_PATHS);

        $this->skipRules = $skipRulesFactory->create();

        $this->excludedPaths = $excludePaths;
        $this->fileInfoMatcher = $fileInfoMatcher;
        $this->skipVoters = $skipVoters;
    }

    public function shouldSkipElementAndFileInfo($element, SmartFileInfo $smartFileInfo): bool
    {
        foreach ($this->skipVoters as $skipVoter) {
            if ($skipVoter->match($element)) {
                return $skipVoter->shouldSkip($element, $smartFileInfo);
            }
        }

        if ($this->shouldSkipMatchingRuleAndFile($this->skipRules->getSkippedCodes(), $element, $smartFileInfo)) {
            return true;
        }

        return $this->shouldSkipMatchingRuleAndFile($this->skipRules->getSkippedMessages(), $element, $smartFileInfo);
    }

    public function shouldSkipFileInfo(SmartFileInfo $smartFileInfo): bool
    {
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $this->excludedPaths);
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
