<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\CheckerSetExtractor\FixerSetExtractor;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

final class ShowCommand extends Command
{
    /**
     * @var string
     */
    private const NAME = 'show';

    /**
     * @var string
     */
    private const OPTION_FIXER_SET_NAME = 'fixer-set';

    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var FixerSetExtractor
     */
    private $fixerSetExtractor;

    /**
     * @var int
     */
    private $checkersTotal = 0;

    public function __construct(
        SniffFileProcessor $sniffFileProcessor,
        FixerFileProcessor $fixerFileProcessor,
        SymfonyStyle $symfonyStyle,
        FixerSetExtractor $fixerSetExtractor
    ) {
        parent::__construct();

        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->symfonyStyle = $symfonyStyle;
        $this->fixerSetExtractor = $fixerSetExtractor;
    }

    protected function configure(): void
    {
        $this->setName(self::NAME);
        $this->setDescription('Show loaded checkers and their configuration.');
        $this->addOption(
            self::OPTION_FIXER_SET_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            'Fixer sets (@PSR1, @PSR2, @Symfony...)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($fixerSetName = $input->getOption(self::OPTION_FIXER_SET_NAME)) {
            $fixerSet = $this->fixerSetExtractor->extract($fixerSetName);
            $fixerNames = array_keys($fixerSet);
            $this->displayCheckerList($fixerNames, 'PHP-CS-Fixer - fixer set ' . $fixerSetName);
        } else {
            $this->displayCheckerList($this->sniffFileProcessor->getSniffs(), 'PHP_CodeSniffer');
            $this->displayCheckerList($this->fixerFileProcessor->getFixers(), 'PHP-CS-Fixer');
        }

        $this->symfonyStyle->success(sprintf(
            'Loaded %d checkers in total',
            $this->checkersTotal
        ));

        $this->symfonyStyle->newLine();

        return 0;
    }

    /**
     * @param FixerInterface[]|Sniff[]|string $fixers
     */
    private function displayCheckerList(array $fixers, string $type): void
    {
        $checkerNames = array_map(function ($fixer) {
            return is_string($fixer) ? $fixer : get_class($fixer);
        }, $fixers);

        $checkerCount = count($checkerNames);
        if (! $checkerCount) {
            return;
        }

        $this->checkersTotal += $checkerCount;

        $this->symfonyStyle->section(sprintf(
            '%d checkers from %s:',
            count($checkerNames),
            $type
        ));

        sort($checkerNames);

        foreach ($checkerNames as $checkerName) {
            $this->symfonyStyle->text(' - ' . $checkerName);
        }
    }
}
