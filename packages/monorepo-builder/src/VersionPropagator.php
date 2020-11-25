<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\ValueObject\File;

final class VersionPropagator
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @param array<string, string> $filesToVersion
     * @return array<string, string>
     */
    public function processManualConfigFiles(array $filesToVersion, string $packageName, string $newVersion): array
    {
        if (! isset($filesToVersion[File::CONFIG])) {
            return $filesToVersion;
        }

        $message = sprintf(
            'Update "%s" to "%s" version in "%s" file manually',
            $packageName,
            $newVersion,
            File::CONFIG
        );
        $this->symfonyStyle->warning($message);

        unset($filesToVersion[File::CONFIG]);

        return $filesToVersion;
    }
}
