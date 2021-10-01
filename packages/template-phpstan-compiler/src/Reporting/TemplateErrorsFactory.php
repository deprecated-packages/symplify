<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\Reporting;

use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\TemplatePHPStanCompiler\ValueObject\PhpFileContentsWithLineMap;

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
//        ++$desiredLine;

//        if (count($phpToTemplateLines) === 1) {
//            $firstKey = array_key_first($phpToTemplateLines);
//            return $phpToTemplateLines[$firstKey];
//        }

        $lastTemplateLine = null;

        foreach ($phpToTemplateLines as $phpLine => $templateLine) {
            if ($desiredLine <= $phpLine) {
                $lastTemplateLine = $templateLine;
                continue;
            }

            // find nearest neighbor - in case of multiline PHP replacement per one latte line
            return $templateLine;
        }

        return $lastTemplateLine;
    }
}
