<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\Reporting;

use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\TemplatePHPStanCompiler\ValueObject\PhpFileContentsWithLineMap;

/**
 * @api
 */
final class TemplateErrorsFactory
{
    /**
     * @return RuleError[]
     */
    public function createErrors(
        array $errors,
        string $filePath,
        string $resolvedTemplateFilePath,
        PhpFileContentsWithLineMap $phpFileContentsWithLineMap,
        int $phpFileLine
    ): array {
        $ruleErrors = [];

        $phpToTemplateLines = $phpFileContentsWithLineMap->getPhpToTemplateLines();

        $templateFileInfo = new SmartFileInfo($resolvedTemplateFilePath);
        $realPath = $templateFileInfo->getRealPath();

        foreach ($errors as $error) {
            // correct error PHP line number to Latte line number
            $errorLine = (int) $error->getLine();
            $templateLine = $this->resolveNearestPhpLine($phpToTemplateLines, $errorLine);

            $ruleErrors[] = RuleErrorBuilder::message($error->getMessage())
                ->file($filePath)
                ->line($phpFileLine)
                ->metadata([
                    'template_file_path' => $realPath,
                    'template_line' => $templateLine,
                ])
                ->build();
        }

        return $ruleErrors;
    }

    /**
     * @param array<int, int> $phpToTemplateLines
     */
    private function resolveNearestPhpLine(array $phpToTemplateLines, int $desiredLine): int
    {
        $lastTemplateLine = 1;

        foreach ($phpToTemplateLines as $phpLine => $templateLine) {
            if ($desiredLine > $phpLine) {
                $lastTemplateLine = $templateLine;
                continue;
            }

            // find nearest neighbor - in case of multiline PHP replacement per one latte line
            return $templateLine;
        }

        return $lastTemplateLine;
    }
}
