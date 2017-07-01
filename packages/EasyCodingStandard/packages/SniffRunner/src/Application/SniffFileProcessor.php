<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SplFileInfo;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;
use Symplify\EasyCodingStandard\SniffRunner\TokenDispatcher\Event\FileTokenEvent;
use Symplify\EasyCodingStandard\SniffRunner\TokenDispatcher\TokenDispatcher;

final class SniffFileProcessor implements FileProcessorInterface
{
    /**
     * @var TokenDispatcher
     */
    private $tokenDispatcher;

    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var Sniff[]
     */
    private $sniffs = [];

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var bool
     */
    private $isFixer = false;

    public function __construct(
        TokenDispatcher $tokenDispatcher,
        Fixer $fixer,
        FileFactory $fileFactory,
        Configuration $configuration
    ) {
        $this->tokenDispatcher = $tokenDispatcher;
        $this->fixer = $fixer;
        $this->fileFactory = $fileFactory;
        $this->configuration = $configuration;
        $this->addCompatibilityLayer();
    }

    public function setSingleSniff(Sniff $sniff): void
    {
        $this->tokenDispatcher->addSingleSniffListener($sniff);
    }

    public function addSniff(Sniff $sniff): void
    {
        $this->sniffs[] = $sniff;
    }

    /**
     * @return Sniff[]
     */
    public function getSniffs(): array
    {
        return $this->sniffs;
    }

    public function processFile(SplFileInfo $fileInfo, bool $dryRun = false): void
    {
        $file = $this->fileFactory->createFromFileInfo($fileInfo, $this->isFixer());

        $this->tokenDispatcher->addSniffListeners($this->sniffs);

        if ($this->isFixer() === false) {
            $this->processFileWithoutFixer($file);
        } else {
            $this->processFileWithFixer($file, $dryRun);
        }
    }

    private function processFileWithoutFixer(File $file): void
    {
        foreach ($file->getTokens() as $stackPointer => $token) {
            $this->tokenDispatcher->dispatchToken(
                $token['code'],
                new FileTokenEvent($file, $stackPointer)
            );
        }
    }

    private function processFileWithFixer(File $file, bool $dryRun = false): void
    {
        // 1. puts tokens into fixer
        $this->fixer->startFile($file);

        // 2. run all Sniff fixers
        $this->processFileWithoutFixer($file);

        // 3. content has changed, save it!
        $newContent = $this->fixer->getContents();

        if ($dryRun === false) {
            file_put_contents($file->getFilename(), $newContent);
        }
    }

    private function addCompatibilityLayer(): void
    {
        if (! defined('PHP_CODESNIFFER_VERBOSITY')) {
            define('PHP_CODESNIFFER_VERBOSITY', 0);
        }
        new Tokens;
    }

    /**
     */
    public function setIsFixer(bool $isFixer)
    {
        $this->isFixer = $isFixer;
    }

    private function isFixer(): bool
    {
        return $this->isFixer || $this->configuration->isFixer();
    }
}
