<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Formatter;

use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;

final class RequiredWithMessageFormatter
{
    public function __construct(
        private ArrayStringAndFnMatcher $arrayStringAndFnMatcher
    ) {
    }

    /**
     * @param array<string, string|null> $forbiddenFunctions
     */
    public function formatError(string $errorMessage, string $funcName, array $forbiddenFunctions): string
    {
        foreach ($forbiddenFunctions as $forbiddenFunction => $additionalMessage) {
            if (! $additionalMessage) {
                continue;
            }

            if (! $this->arrayStringAndFnMatcher->isMatch($funcName, [$forbiddenFunction])) {
                continue;
            }

            return sprintf($errorMessage . ': ' . $additionalMessage, $funcName);
        }

        return sprintf($errorMessage, $funcName);
    }

    /**
     * @param string[]|array<string|int, string> $forbiddenFunctions
     * @return array<string, string|null> forbidden functions as keys, optional additional messages as values
     */
    public function normalizeConfig(array $forbiddenFunctions): array
    {
        $valuesToMessages = [];
        foreach ($forbiddenFunctions as $key => $value) {
            $funcName = null;
            $additionalMessage = null;

            if (is_int($key)) {
                $funcName = $value;
                $additionalMessage = null;
            } elseif (is_string($key)) {
                // 'key': 'value'
                $funcName = $key;
                $additionalMessage = $value;
            }

            if ($funcName === null) {
                continue;
            }

            if ($additionalMessage === '') {
                $valuesToMessages[$funcName] = null;
            } else {
                $valuesToMessages[$funcName] = $additionalMessage;
            }
        }

        return $valuesToMessages;
    }
}
