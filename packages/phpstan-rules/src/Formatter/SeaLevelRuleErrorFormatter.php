<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Formatter;

use Nette\Utils\Strings;

final class SeaLevelRuleErrorFormatter {

    /**
     * @param string[] $errors
     * @return string[]
     */
    public function formatErrors(
        string $message,
        float $minimalLevel,
        int $propertyCount,
        int $typedPropertyCount,
        array $errors
    ): array {

        if ($propertyCount === 0) {
            return [];
        }

        $propertyTypeDeclarationSeaLevel = $typedPropertyCount / $propertyCount;

        // has the code met the minimal sea level of types?
        if ($propertyTypeDeclarationSeaLevel >= $minimalLevel) {
            return [];
        }

        $errorMessage = sprintf(
            $message,
            $propertyCount,
            $propertyTypeDeclarationSeaLevel * 100,
            $minimalLevel * 100
        );

        if (count($errors) > 0) {
            $errorMessage .= PHP_EOL . PHP_EOL;
            $errorMessage .= implode(PHP_EOL . PHP_EOL, $errors);
            $errorMessage .= PHP_EOL;

            // keep error printable
            $errorMessage = Strings::truncate($errorMessage, 8000);
        }

        return [$errorMessage];
    }
}
