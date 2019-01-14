<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Command\Reporter;

use Symfony\Component\Console\Style\SymfonyStyle;
use function Safe\sprintf;

final class MigrateJekyllReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public function reportPathOperation(string $action, string $oldPath, string $newPath): void
    {
        if (is_dir($oldPath)) {
            $this->symfonyStyle->note(sprintf('%s directory "%s" to "%s"', $action, $oldPath, $newPath));
        } elseif (is_file($oldPath)) {
            $this->symfonyStyle->note(sprintf('%s file "%s" to "%s', $action, $oldPath, $newPath));
        }
    }
}
