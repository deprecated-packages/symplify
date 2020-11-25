<?php

declare(strict_types=1);

namespace Symplify\Skipper\Skipper;

use Symplify\Skipper\Contract\SkipVoterInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

final class Skipper
{
    /**
     * @var string
     */
    private const FILE_ELEMENT = 'file_elements';

    /**
     * @var SkipVoterInterface[]
     */
    private $skipVoters = [];

    /**
     * @param SkipVoterInterface[] $skipVoters
     */
    public function __construct(array $skipVoters)
    {
        $this->skipVoters = $skipVoters;
    }

    public function shouldSkipFileInfo(SmartFileInfo $smartFileInfo): bool
    {
        return $this->shouldSkipElementAndFileInfo(self::FILE_ELEMENT, $smartFileInfo);
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
}
