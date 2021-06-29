<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Formatter;

use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\PHPStanRules\ValueObject\Configuration\RequiredWithMessage;

final class RequiredWithMessageFormatter
{
    /**
     * @param string[]|array<string|int, string> $configuration
     * @return RequiredWithMessage[] forbidden values as keys, optional helpful messages as value
     */
    public function normalizeConfig(array $configuration): array
    {
        $requiredWithMessages = [];

        foreach ($configuration as $key => $value) {
            if (is_int($key)) {
                $requiredWithMessages[] = new RequiredWithMessage($value, null);
            } elseif (is_string($key)) {
                $requiredWithMessages[] = new RequiredWithMessage($key, $value);
            } else {
                throw new ShouldNotHappenException();
            }
        }

        return $requiredWithMessages;
    }
}
