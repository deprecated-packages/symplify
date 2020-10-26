<?php

declare(strict_types=1);

namespace Symplify\Skipper\ValueObject;

final class SkipRules
{
    /**
     * @var array<string, string[]|null>
     */
    private $skippedClasses = [];

    /**
     * @var string[]
     */
    private $skippedCodes = [];

    /**
     * @var array<string, string[]|null>
     */
    private $skippedMessages = [];

    /**
     * @param string[] $skippedClasses
     * @param string[] $skippedCodes
     * @param array<string, string[]|null> $skippedMessages
     */
    public function __construct(array $skippedClasses, array $skippedCodes, array $skippedMessages)
    {
        $this->skippedClasses = $skippedClasses;
        $this->skippedCodes = $skippedCodes;
        $this->skippedMessages = $skippedMessages;
    }

    /**
     * @return array<string, string[]|null>
     */
    public function getSkippedClasses(): array
    {
        return $this->skippedClasses;
    }

    /**
     * @return string[]
     */
    public function getSkippedCodes(): array
    {
        return $this->skippedCodes;
    }

    /**
     * @return array<string, string[]|null>
     */
    public function getSkippedMessages(): array
    {
        return $this->skippedMessages;
    }
}
