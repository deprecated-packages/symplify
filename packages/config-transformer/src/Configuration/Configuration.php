<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Configuration;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\ConfigTransformer\Guard\InputValidator;
use Symplify\ConfigTransformer\ValueObject\Format;
use Symplify\ConfigTransformer\ValueObject\Option;
use Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

final class Configuration implements SymfonyVersionFeatureGuardInterface
{
    /**
     * @var string[]
     */
    private const ALLOWED_OUTPUT_FORMATS = [Format::YAML, Format::PHP];

    /**
     * @var string[]
     */
    private const ALLOWED_INPUT_FORMATS = [Format::XML, Format::YML, Format::YAML];

    /**
     * @var string
     */
    private $outputFormat;

    /**
     * @var string
     */
    private $inputFormat;

    /**
     * @var string[]
     */
    private $source = [];

    /**
     * @var float
     */
    private $targetSymfonyVersion;

    /**
     * @var bool
     */
    private $isDryRun = false;

    /**
     * @var InputValidator
     */
    private $inputValidator;

    public function __construct(InputValidator $inputValidator)
    {
        $this->inputValidator = $inputValidator;
    }

    public function populateFromInput(InputInterface $input): void
    {
        $this->source = (array) $input->getArgument(Option::SOURCES);
        $this->targetSymfonyVersion = floatval($input->getOption(Option::TARGET_SYMFONY_VERSION));
        $this->isDryRun = boolval($input->getOption(Option::DRY_RUN));

        $this->resolveInputFormat($input);
        $this->resolveOutputFormat($input);
    }

    public function getOutputFormat(): string
    {
        return $this->outputFormat;
    }

    public function getSource(): array
    {
        return $this->source;
    }

    public function isAtLeastSymfonyVersion(float $symfonyVersion): bool
    {
        return $this->targetSymfonyVersion >= $symfonyVersion;
    }

    public function isDryRun(): bool
    {
        return $this->isDryRun;
    }

    public function getInputFormat(): string
    {
        return $this->inputFormat;
    }

    public function changeSymfonyVersion(float $symfonyVersion): void
    {
        $this->targetSymfonyVersion = $symfonyVersion;
    }

    public function changeInputFormat(string $inputFormat): void
    {
        $this->setInputFormat($inputFormat);
    }

    public function changeOutputFormat(string $outputFormat): void
    {
        $this->setOutputFormat($outputFormat);
    }

    /**
     * @return string[]
     */
    public function getInputSuffixes(): array
    {
        if ($this->inputFormat === Format::YAML) {
            return [Format::YAML, Format::YML];
        }

        return [$this->inputFormat];
    }

    private function resolveInputFormat(InputInterface $input): void
    {
        /** @var string $inputFormat */
        $inputFormat = (string) $input->getOption(Option::INPUT_FORMAT);
        $inputFormat = $this->resolveEmptyInputFallback($input, $inputFormat);

        $this->setInputFormat($inputFormat);
    }

    private function resolveOutputFormat(InputInterface $input): void
    {
        /** @var string $outputFormat */
        $outputFormat = (string) $input->getOption(Option::OUTPUT_FORMAT);

        $this->setOutputFormat($outputFormat);
    }

    private function setOutputFormat(string $outputFormat): void
    {
        $this->inputValidator->validateFormatValue(
            $outputFormat,
            self::ALLOWED_OUTPUT_FORMATS,
            Option::OUTPUT_FORMAT,
            'output'
        );

        $this->outputFormat = $outputFormat;
    }

    private function setInputFormat(string $inputFormat): void
    {
        $this->inputValidator->validateFormatValue(
            $inputFormat,
            self::ALLOWED_INPUT_FORMATS,
            Option::INPUT_FORMAT,
            'input'
        );
        if ($inputFormat === Format::YML) {
            $inputFormat = Format::YAML;
        }

        $this->inputFormat = $inputFormat;
    }

    /**
     * Autoresolve input format in case of 1 file is provided and no "--input-format"
     */
    private function resolveEmptyInputFallback(InputInterface $input, string $inputFormat): string
    {
        if ($inputFormat !== '') {
            return $inputFormat;
        }

        $source = (array) $input->getArgument(Option::SOURCES);

        // nothing we can do
        if (count($source) !== 1) {
            return '';
        }

        $singleSource = $source[0];
        if (! file_exists($singleSource)) {
            return '';
        }

        if (! is_file($singleSource)) {
            return '';
        }

        $sourceFileInfo = new SmartFileInfo($singleSource);
        return $sourceFileInfo->getSuffix();
    }
}
