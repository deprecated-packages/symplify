<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Forbidden;

use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;

final class ForbiddenCallable {
   public function __construct(
        private ArrayStringAndFnMatcher $arrayStringAndFnMatcher
   ) {
   }

    /**
     * @param string $errorMessage
     * @param string $funcName
     * @param array<string, string|null> $forbiddenFunctions
     *
     * @return string
     */
    public function formatError(string $errorMessage, string $funcName, array $forbiddenFunctions): string {
        foreach($forbiddenFunctions as $forbiddenFunction => $additionalMessage) {
            if (!$additionalMessage) {
                continue;
            }

            if (! $this->arrayStringAndFnMatcher->isMatch($funcName, [$forbiddenFunction])) {
                continue;
            }

            return sprintf($errorMessage .': '. $additionalMessage, $funcName);
        }
        return sprintf($errorMessage, $funcName);
    }

    /**
     * @param string[]|array<string, string>|list<array<string, string>> $forbiddenFunctions
     *
     * @return array<string, string|null> forbidden functions as keys, optional additional messages as values
     */
    public function normalizeConfig($forbiddenFunctions): array {
        $forbidden = [];
        foreach($forbiddenFunctions as $key => $value) {
            if (is_int($key)) {
                if (is_array($value)) {
                    /**
                     * config-format:
                     *
                     * forbiddenFunctions:
                     * - 'extract': 'you shouldn"t use this dynamic things'
                     * - 'dump': 'seems you missed some debugging function'
                     */

                    $aKey = array_key_first($value);
                    $aVal = $value[$aKey];

                    if ($aVal === '') {
                        $forbidden[$aKey] = null;
                    } else {
                        $forbidden[$aKey] = $aVal;
                    }
                } else {
                    /**
                     * config-format:
                     *
                     * forbiddenFunctions:
                     * - 'extract'
                     * - 'dump'
                     */

                    $forbidden[$value] = null;
                }
            } elseif (is_string($key)) {
                /**
                 * config-format:
                 *
                 * forbiddenFunctions:
                 *   'extract': 'you shouldn"t use this dynamic things'
                 *   'dump': 'seems you missed some debugging function'
                 */

                if ($value === '') {
                    $forbidden[$key] = null;
                } else {
                    $forbidden[$key] = $value;
                }
            }
        }

        return $forbidden;
    }
}
