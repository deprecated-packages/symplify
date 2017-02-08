<?php declare(strict_types=1);

namespace Symplify\MultiCodingStandard\PhpCsFixer\Application;

use ArrayIterator;
use Symplify\MultiCodingStandard\PhpCsFixer\Application\Command\RunApplicationCommand;
use Symplify\MultiCodingStandard\PhpCsFixer\Factory\FixerFactory;
use Symplify\MultiCodingStandard\PhpCsFixer\Report\DiffDataCollector;
use Symplify\MultiCodingStandard\PhpCsFixer\Runner\RunnerFactory;
use Symplify\SniffRunner\File\Finder\SourceFinder;

final class Application
{
    /**
     * @var FixerFactory
     */
    private $fixerSetFactory;
    /**
     * @var SourceFinder
     */
    private $sourceFinder;

    /**
     * @var DiffDataCollector
     */
    private $diffDataCollector;

    public function __construct(
        FixerFactory $fixerSetFactory,
        SourceFinder $sourceFinder,
        DiffDataCollector $diffDataCollector,
        RunnerFactory $runnerFactory
    ) {
        $this->fixerSetFactory = $fixerSetFactory;
        $this->sourceFinder = $sourceFinder;
        $this->diffDataCollector = $diffDataCollector;
    }

    public function runCommand(RunApplicationCommand $command)
    {
        $this->registerFixersToFixer($command->getFixerLevels(), $command->getFixers(), $command->getExcludeFixers());

        $this->runForSource($command->getSource(), $command->isFixer());
    }

    private function registerFixersToFixer(array $fixerLevels, array $fixers, array $excludedFixers)
    {
        $fixers = $this->fixerSetFactory->createRulesAndExcludedRules(
            $fixerLevels, $fixers, $excludedFixers
        );

        $this->fixer->registerCustomFixers($fixers);
    }

    private function runForSource(array $source, bool $isFixer)
    {
        $this->registerSourceToFixer($source);

        /** @var Config $config */
        $config = $this->fixer->getConfigs()[0];
        $config->fixers($this->fixer->getFixers());

        $changedDiffs = $this->fixer->fix($config, !$isFixer, !$isFixer);
        $this->diffDataCollector->setDiffs($changedDiffs);
    }

    private function registerSourceToFixer(array $source)
    {
        $files = $this->sourceFinder->find($source);

        $config = new Config();
        $config->finder(new ArrayIterator($files));
        $this->fixer->addConfig($config);
    }
}
