<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\MigratorJekyll\JekyllToStatieMigrator;
use function Safe\getcwd;

final class MigrateJekyllCommand extends Command
{
    /**
     * @var mixed[]
     */
    private $migratorJekyll = [];

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var JekyllToStatieMigrator
     */
    private $jekyllToStatieMigrator;

    /**
     * @param mixed[] $migratorJekyll
     */
    public function __construct(
        array $migratorJekyll,
        SymfonyStyle $symfonyStyle,
        JekyllToStatieMigrator $jekyllToStatieMigrator
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->jekyllToStatieMigrator = $jekyllToStatieMigrator;
        $this->migratorJekyll = $migratorJekyll;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Migrates Jekyll website to Statie');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->jekyllToStatieMigrator->migrate(getcwd(), $this->migratorJekyll);

        $this->symfonyStyle->success('Migration finished!');
        $this->symfonyStyle->note('Run "npm install" and "gulp" to see your new website');

        return ShellCode::SUCCESS;
    }
}
