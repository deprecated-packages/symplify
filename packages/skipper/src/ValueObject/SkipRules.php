<?php

declare(strict_types=1);

namespace Symplify\Skipper\ValueObject;

final class SkipRules
{
    /**
     * @var array<string, string[]|null>
     */
    private $skippedMessages = [];

    /**
     * @param array<string, string[]|null> $skippedMessages
     */
    public function __construct(array $skippedMessages)
    {
        $this->skippedMessages = $skippedMessages;
    }

    /**
     * @return array<string, string[]|null>
     */
    public function getSkippedMessages(): array
    {
        return $this->skippedMessages;
    }
}
