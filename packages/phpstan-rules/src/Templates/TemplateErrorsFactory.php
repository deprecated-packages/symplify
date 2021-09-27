<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Templates;

use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject\PhpFileContentsWithLineMap;

final class TemplateErrorsFactory
{
    /**
     * @return RuleError[]
     */
    public function createErrors(
        array $errors,
        string $resolvedTemplateFilePath,
        PhpFileContentsWithLineMap $phpFileContentsWithLineMap
    ): array {
        $ruleErrors = [];

        $phpToTemplateLines = $phpFileContentsWithLineMap->getPhpToTemplateLines();

        foreach ($errors as $error) {
            // correct error PHP line number to Latte line number
            $errorLine = (int) $error->getLine();
            $errorLine = $this->resolveNearestPhpLine($phpToTemplateLines, $errorLine);

            $ruleErrors[] = RuleErrorBuilder::message($error->getMessage())
                ->file($resolvedTemplateFilePath)
                ->line($errorLine)
                ->build();
        }

        return $ruleErrors;
    }

    /**
     * @param array<int, int> $phpToTemplateLines
     */
    private function resolveNearestPhpLine(array $phpToTemplateLines, int $desiredLine): int
    {
        foreach ($phpToTemplateLines as $phpLine => $latteLine) {
            if ($desiredLine < $phpLine) {
                continue;
            }

            // find nearest neighbor - in case of multiline PHP replacement per one latte line
            return $latteLine;
        }

        return $desiredLine;
    }
}
