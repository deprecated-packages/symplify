<?php

declare(strict_types=1);

namespace Symplify\Skipper\Skipper;

use Symplify\Skipper\Contract\SkipVoterInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\Skipper\Tests\Skipper\Skipper\SkipperTest
 */
final class Skipper
{
    /**
     * @var string
     */
    private const FILE_ELEMENT = 'file_elements';

    /**
     * @param SkipVoterInterface[] $skipVoters
     */
    public function __construct(
        private array $skipVoters
    ) {
    }

    public function shouldSkipElement(string | object $element): bool
    {
        $fileInfo = new SmartFileInfo(__FILE__);
        return $this->shouldSkipElementAndFileInfo($element, $fileInfo);
    }

    public function shouldSkipFileInfo(SmartFileInfo $smartFileInfo): bool
    {
        return $this->shouldSkipElementAndFileInfo(self::FILE_ELEMENT, $smartFileInfo);
    }

    public function shouldSkipElementAndFileInfo(string | object $element, SmartFileInfo $smartFileInfo): bool
    {
        foreach ($this->skipVoters as $skipVoter) {
            if ($skipVoter->match($element)) {
                return $skipVoter->shouldSkip($element, $smartFileInfo);
            }
        }

        return false;
    }
}
