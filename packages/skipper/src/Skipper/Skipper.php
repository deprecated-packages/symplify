<?php

declare(strict_types=1);

namespace Symplify\Skipper\Skipper;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Skipper\Contract\SkipVoterInterface;
use Symplify\Skipper\Matcher\FileInfoMatcher;
use Symplify\Skipper\ValueObject\Option;
use Symplify\SmartFileSystem\SmartFileInfo;

final class Skipper
{
    /**
     * @var string[]
     */
    private $excludedPaths = [];

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
        FileInfoMatcher $fileInfoMatcher,
        array $skipVoters
    ) {
        $this->excludedPaths = $parameterProvider->provideArrayParameter(Option::EXCLUDE_PATHS);
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

        return false;
    }

    public function shouldSkipFileInfo(SmartFileInfo $smartFileInfo): bool
    {
        return $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $this->excludedPaths);
    }
}
