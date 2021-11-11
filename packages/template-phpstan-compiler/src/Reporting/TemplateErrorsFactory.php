<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\Reporting;

use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\TemplatePHPStanCompiler\ValueObject\ErrorMessageWithTip;
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
        $relativeFilePathFromCwd = $templateFileInfo->getRelativeFilePathFromCwd();

        foreach ($errors as $error) {
            // correct error PHP line number to Latte line number
            $errorLine = (int) $error->getLine();
            $templateLine = $this->resolveNearestPhpLine($phpToTemplateLines, $errorLine);
            $errorMessageWithTip = $this->resolveTipForMessage($error->getMessage());

            $ruleErrorBuilder = RuleErrorBuilder::message($errorMessageWithTip->getErrorMessage())
                ->file($filePath)
                ->line($phpFileLine)
                ->metadata([
                    'template_file_path' => $relativeFilePathFromCwd,
                    'template_line' => $templateLine,
                ]);

            $tip = $errorMessageWithTip->getTip();
            if ($tip) {
                $ruleErrorBuilder->tip($tip);
            }

            $ruleErrors[] = $ruleErrorBuilder->build();
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

    private function resolveTipForMessage(string $message): ErrorMessageWithTip
    {
        $tip = '';
        $undefinedFilter = str_replace('Access to an undefined property Latte\Runtime\FilterExecutor::$', '', $message);
        if ($undefinedFilter !== $message) {
            $message = 'Undefined filter ' . $undefinedFilter;
            $tip = 'Register it in parameters > latteFilters. See https://github.com/symplify/symplify/tree/main/packages/phpstan-latte-rules#configuration';
        }
        return new ErrorMessageWithTip($message, $tip);
    }
}
