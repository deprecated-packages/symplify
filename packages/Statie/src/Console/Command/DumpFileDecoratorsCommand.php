<?php declare(strict_types=1);

namespace Symplify\Statie\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\Renderable\RenderableFilesProcessor;
use function Safe\sprintf;

final class DumpFileDecoratorsCommand extends Command
{
    /**
     * @var RenderableFilesProcessor
     */
    private $renderableFilesProcessor;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(RenderableFilesProcessor $renderableFilesProcessor, SymfonyStyle $symfonyStyle)
    {
        parent::__construct();
        $this->renderableFilesProcessor = $renderableFilesProcessor;
        $this->symfonyStyle = $symfonyStyle;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Dump loaded file decorators with their priority');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->renderableFilesProcessor->getFileDecorators() as $fileDecorator) {
            $this->symfonyStyle->note(
                sprintf('%s with %d priority', get_class($fileDecorator), $fileDecorator->getPriority())
            );
        }

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }
}
