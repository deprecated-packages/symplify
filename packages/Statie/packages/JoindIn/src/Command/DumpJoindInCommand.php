<?php declare(strict_types=1);

namespace Symplify\Statie\JoindIn\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\JoindIn\Api\JoindInApi;
use function Safe\sprintf;

final class DumpJoindInCommand extends Command
{
    /**
     * @var string
     */
    private $joinedInName;

    /**
     * @var JoindInApi
     */
    private $joindInApi;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(string $joindInUsername, JoindInApi $joindInApi, SymfonyStyle $symfonyStyle)
    {
        parent::__construct();
        $this->joinedInName = $joindInUsername;
        $this->joindInApi = $joindInApi;
        $this->symfonyStyle = $symfonyStyle;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generate joindin talks and data by username');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $talks = $this->joindInApi->getTalks($this->joinedInName);

        if (count($talks) === 0) {
            $this->symfonyStyle->note(sprintf('Found 0 talks for "%s" username', $this->joinedInName));

            return ShellCode::SUCCESS;
        }

        // dump to "$source_directory%/_data/generated/joindin.yaml"
        die;
    }
}
