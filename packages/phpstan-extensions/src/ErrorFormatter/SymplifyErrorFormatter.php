<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\ErrorFormatter;

use Nette\Utils\Strings;
use PHPStan\Analyser\Error;
use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use PHPStan\Command\Output;
use PHPStan\Command\Symfony\SymfonyStyle;
use Symfony\Component\Console\Terminal;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\PHPStanExtensions\Tests\ErrorFormatter\SymplifyErrorFormatterTest
 */
final class SymplifyErrorFormatter implements ErrorFormatter
{
    /**
     * To fit in Linux/Windows terminal windows to prevent overflow.
     * @var int
     */
    private const BULGARIAN_CONSTANT = 8;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var Terminal
     */
    private $terminal;

    /**
     * @var Output
     */
    private $output;

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    public function formatErrors(AnalysisResult $analysisResult, Output $output): int
    {
        /** @var SymfonyStyle $consoleStyle */
        $consoleStyle = $output->getStyle();
        $this->output = $output;
        $this->symfonyStyle = $consoleStyle;

        if ($analysisResult->getTotalErrorsCount() === 0 && $analysisResult->getWarnings() === []) {
            $this->symfonyStyle->success('No errors');
            return ShellCode::SUCCESS;
        }

        $this->reportErrors($analysisResult);

        foreach ($analysisResult->getNotFileSpecificErrors() as $notFileSpecificError) {
            $this->symfonyStyle->warning($notFileSpecificError);
        }

        foreach ($analysisResult->getWarnings() as $warning) {
            $this->symfonyStyle->warning($warning);
        }

        return ShellCode::ERROR;
    }

    private function reportErrors(AnalysisResult $analysisResult): void
    {
        if ($analysisResult->getFileSpecificErrors() === []) {
            return;
        }

        $fileSpecificErrorsByMessage = $this->groupFileSpecificErrorsByMessage($analysisResult);

        foreach ($fileSpecificErrorsByMessage as $message => $errors) {
            if (count($errors) === 1) {
                // @todo print with "path" only, but also specific!
                $this->printSingleError($errors[0]);
            } else {
                $this->printMultiFileErrors($message, $errors);
            }
        }

        $this->symfonyStyle->newLine(1);

        $errorMessage = sprintf('Found %d errors', $analysisResult->getTotalErrorsCount());
        $this->symfonyStyle->error($errorMessage);
    }

    private function separator(): void
    {
        $separator = str_repeat('-', $this->terminal->getWidth() - self::BULGARIAN_CONSTANT);
        $this->writeln($separator);
    }

    private function getRelativePath(string $filePath): string
    {
        // remove trait clutter
        $clearFilePath = Strings::replace($filePath, '#(?<file>.*?)(\s+\(in context.*?)?$#', '$1');
        if (! file_exists($clearFilePath)) {
            return $clearFilePath;
        }

        $smartFileInfo = new SmartFileInfo($clearFilePath);
        return $smartFileInfo->getRelativeFilePathFromCwd();
    }

    private function regexMessage(string $message): string
    {
        // remove extra ".", that is really not part of message
        $message = rtrim($message, '.');
        return '#' . preg_quote($message, '#') . '#';
    }

    private function writeln(string $separator): void
    {
        $this->output->writeLineFormatted(' ' . $separator);
    }

    /**
     * @return array<string, Error[]>
     */
    private function groupFileSpecificErrorsByMessage(AnalysisResult $analysisResult): array
    {
        $errorsByFile = [];

        foreach ($analysisResult->getFileSpecificErrors() as $fileSpecificError) {
            $errorsByFile[$fileSpecificError->getMessage()][] = $fileSpecificError;
        }

        // sort values with multiple files per error last
        uasort($errorsByFile, function (array $firstErrors, array $secondErrors): int {
            return count($firstErrors) <=> count($secondErrors);
        });

        return $errorsByFile;
    }

    private function printSingleError(Error $error): void
    {
        $this->separator();

        // clickable path
        $relativeFilePath = $this->getRelativePath($error->getFile());
        $this->writeln(' ' . $relativeFilePath . ':' . $error->getLine());
        $this->separator();

        // ignored path
        $regexMessage = $this->regexMessage((string) $error->getMessage());
        $itemMessage = sprintf(" - '%s'", $regexMessage);
        $this->writeln($itemMessage);

        $this->separator();
        $this->symfonyStyle->newLine();
    }

    /**
     * @param Error[] $errors
     */
    private function printMultiFileErrors(string $message, array $errors): void
    {
        $this->writeln('-');
        $this->writeln("    message: '" . $this->regexMessage($message) . "'");
        $this->writeln('    paths:');

        // uniquate errors per file
        $errorsByFile = [];
        foreach ($errors as $error) {
            $relativeFilePath = $this->getRelativePath($error->getFile());
            $errorsByFile[$relativeFilePath] = $error;
        }

        foreach ($errorsByFile as $error) {
            $relativeFilePath = $this->getRelativePath($error->getFile());
            $this->writeln('        - ' . $relativeFilePath);
        }

        $this->symfonyStyle->newLine();
    }
}
