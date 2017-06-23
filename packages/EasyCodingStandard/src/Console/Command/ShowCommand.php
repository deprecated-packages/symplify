<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorCollectorInterface;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;

final class ShowCommand extends Command implements FileProcessorCollectorInterface
{
    /**
     * @var string
     */
    private const NAME = 'show';

    /**
     * @var EasyCodingStandardStyle
     */
    private $style;

    /**
     * @var FileProcessorInterface[]
     */
    private $fileProcessor = [];

    public function __construct(EasyCodingStandardStyle $style)
    {
        parent::__construct();

        $this->style = $style;
    }

    public function addFileProcessor(FileProcessorInterface $fileProcessor): void
    {
        $this->fileProcessor[] = $fileProcessor;
    }

    protected function configure(): void
    {
        $this->setName(self::NAME);
        $this->setDescription('Show loaded checkers and their configuration.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->style->success('ASDF');

        dump($this->fileProcessor);
        die;

        return 0;
    }
}
