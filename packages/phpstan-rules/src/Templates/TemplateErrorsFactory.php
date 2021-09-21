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
            $errorLine = $phpToTemplateLines[$errorLine] ?? $errorLine;

            $ruleErrors[] = RuleErrorBuilder::message($error->getMessage())
                ->file($resolvedTemplateFilePath)
                ->line($errorLine)
                ->build();
        }

        return $ruleErrors;
    }
}
