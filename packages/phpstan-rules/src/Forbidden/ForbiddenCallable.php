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
     * @return array<string, string|null> $forbiddenFunctions
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
}
