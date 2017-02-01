<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Application;

use Symplify\PHP7_CodeSniffer\Application\Command\RunApplicationCommand;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\EventDispatcher\SniffDispatcher;
use Symplify\PHP7_CodeSniffer\File\Provider\FilesProvider;
use Symplify\PHP7_CodeSniffer\Legacy\LegacyCompatibilityLayer;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\SniffFactory;

final class Application
{
    /**
     * @var SniffDispatcher
     */
    private $sniffDispatcher;

    /**
     * @var FilesProvider
     */
    private $filesProvider;

    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    /**
     * @var FileProcessor
     */
    private $fileProcessor;
    /**
     *
     */
    private $sniffFactory;

    public function __construct(
        SniffDispatcher $sniffDispatcher,
        FilesProvider $sourceFilesProvider,
        ConfigurationResolver $configurationResolver,
        FileProcessor $fileProcessor,
        SniffFactory $sniffFactory
    ) {
        $this->sniffDispatcher = $sniffDispatcher;
        $this->filesProvider = $sourceFilesProvider;
        $this->configurationResolver = $configurationResolver;
        $this->fileProcessor = $fileProcessor;
        $this->sniffFactory = $sniffFactory;

        LegacyCompatibilityLayer::add();
    }

    public function runCommand(RunApplicationCommand $command)
    {
        $command = $this->resolveCommandConfiguration($command);

        // resolve sniffs: $command->getExcludedSniffs()
        // @todo: calculate sniffs to find

        $this->createAndRegisterSniffsToSniffDispatcher($command->getStandards(), $command->getSniffs());

        $this->runForSource($command->getSource(), $command->isFixer());
    }

    private function createAndRegisterSniffsToSniffDispatcher(array $sniffClasses)
    {
        // @todo: have resolved sniffs here
        $sniffs = [];
        foreach ($sniffClasses as $sniffClass) {
            $sniffs[] = $this->sniffFactory->create($sniffClass);
        }
        $this->sniffDispatcher->addSniffListeners($sniffs);
    }

    private function runForSource(array $source, bool $isFixer)
    {
        $files = $this->filesProvider->getFilesForSource($source, $isFixer);
        $this->fileProcessor->processFiles($files, $isFixer);
    }

    private function resolveCommandConfiguration(RunApplicationCommand $command) : RunApplicationCommand
    {
        return new RunApplicationCommand(
            $command->getSource(),
            $this->configurationResolver->resolve('standards', $command->getStandards()),
            $this->configurationResolver->resolve('sniffs', $command->getSniffs()),
            $this->configurationResolver->resolve('sniffs', $command->getExcludedSniffs()),
            $command->isFixer()
        );
    }
}
