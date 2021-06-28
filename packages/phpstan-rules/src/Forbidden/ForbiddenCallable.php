<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Forbidden;

use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;

final class ForbiddenCallable {
    /**
     * @param string[]|array<string, string> $forbiddenFunctions
     */
    public function __construct(
        private ArrayStringAndFnMatcher $arrayStringAndFnMatcher,
        private array $forbiddenFunctions
   ) {
   }

    public function formatError(string $errorMessage, string $funcName): string {
        foreach($this->getForbiddenFunctionsWithMessages() as $forbiddenFunction => $additionalMessage) {
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
