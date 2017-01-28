<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\File;

use Nette\FileNotFoundException;
use Symplify\PHP7_CodeSniffer\Application\Fixer;
use Symplify\PHP7_CodeSniffer\Parser\FileToTokensParser;
use Symplify\PHP7_CodeSniffer\Report\ErrorDataCollector;

final class FileFactory
{
    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var ErrorDataCollector
     */
    private $reportCollector;

    /**
     * @var FileToTokensParser
     */
    private $fileToTokenParser;

    public function __construct(
        Fixer $fixer,
        ErrorDataCollector $reportCollector,
        FileToTokensParser $fileToTokenParser
    ) {
        $this->fixer = $fixer;
        $this->reportCollector = $reportCollector;
        $this->fileToTokenParser = $fileToTokenParser;
    }

    public function create(string $filePath, bool $isFixer) : File
    {
        $this->ensureFileExists($filePath);

        $tokens = $this->fileToTokenParser->parseFromFilePath($filePath);

        return new File($filePath, $tokens, $this->fixer, $this->reportCollector, $isFixer);
    }

    private function ensureFileExists(string $filePath)
    {
        if (!is_file($filePath) || !file_exists($filePath)) {
            throw new FileNotFoundException(sprintf(
                'File "%s" was not found.',
                $filePath
            ));
        }
    }
}
