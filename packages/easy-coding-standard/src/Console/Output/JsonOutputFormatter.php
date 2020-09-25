<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Nette\Utils\Json;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\PackageBuilder\Console\ShellCode;

/**
 * @see \Symplify\EasyCodingStandard\Tests\Console\Output\JsonOutputFormatterTest
 */
final class JsonOutputFormatter implements OutputFormatterInterface
{
    /**
     * @var string
     */
    public const NAME = 'json';

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    public function __construct(Configuration $configuration, EasyCodingStandardStyle $easyCodingStandardStyle)
    {
        $this->configuration = $configuration;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }

    public function report(ErrorAndDiffCollector $errorAndDiffCollector, int $processedFilesCount): int
    {
        $json = $this->createJsonContent($errorAndDiffCollector);
        $this->easyCodingStandardStyle->writeln($json);

        $errorCount = $errorAndDiffCollector->getErrorCount();
        return $errorCount === 0 ? ShellCode::SUCCESS : ShellCode::ERROR;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function createJsonContent(ErrorAndDiffCollector $errorAndDiffCollector): string
    {
        $errorsArray = $this->createBaseErrorsArray($errorAndDiffCollector);

        $firstResolvedConfigFileInfo = $this->configuration->getFirstResolvedConfigFileInfo();
        if ($firstResolvedConfigFileInfo !== null) {
            $errorsArray['meta']['config'] = $firstResolvedConfigFileInfo->getRealPath();
        }

        /** @var CodingStandardError[] $errors */
        foreach ($errorAndDiffCollector->getErrors() as $file => $errors) {
            foreach ($errors as $error) {
                $errorsArray['files'][$file]['errors'][] = [
                    'line' => $error->getLine(),
                    'message' => $error->getMessage(),
                    'sourceClass' => $error->getSourceClass(),
                ];
            }
        }

        /** @var FileDiff[] $diffs */
        foreach ($errorAndDiffCollector->getFileDiffs() as $file => $diffs) {
            foreach ($diffs as $diff) {
                $errorsArray['files'][$file]['diffs'][] = [
                    'diff' => $diff->getDiff(),
                    'appliedCheckers' => $diff->getAppliedCheckers(),
                ];
            }
        }

        return Json::encode($errorsArray, Json::PRETTY);
    }

    /**
     * @return mixed[]
     */
    private function createBaseErrorsArray(ErrorAndDiffCollector $errorAndDiffCollector): array
    {
        return [
            'meta' => [
                'version' => $this->configuration->getPrettyVersion(),
            ],
            'totals' => [
                'errors' => $errorAndDiffCollector->getErrorCount(),
                'diffs' => $errorAndDiffCollector->getFileDiffsCount(),
            ],
            'files' => [],
        ];
    }
}
