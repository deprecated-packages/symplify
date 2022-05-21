<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;

final class DeprecationReporter
{
    /**
     * @var array<string, string>
     */
    private const DEPRECATED_SETS_BY_FILE_PATHS = [
        'config/set/symfony.php' => 'SYMFONY',
        'config/set/symfony-risky.php' => 'SYMFONY_RISKY',
        'config/set/php-cs-fixer.php' => 'PHP_CS_FIXER',
        'config/set/php-cs-fixer-risky.php' => 'PHP_CS_FIXER_RISKY',
    ];

    public function reportDeprecatedSets(ContainerBuilder $containerBuilder, InputInterface $input): void
    {
        // report only once on main command run, not on parallel workers
        if ($input->getFirstArgument() !== 'check') {
            return;
        }

        $foundDeprecatedSets = [];

        foreach ($containerBuilder->getResources() as $resource) {
            if (! $resource instanceof FileResource) {
                continue;
            }

            foreach (self::DEPRECATED_SETS_BY_FILE_PATHS as $setFilePath => $setName) {
                if (! str_ends_with($resource->getResource(), $setFilePath)) {
                    continue;
                }

                $foundDeprecatedSets[] = $setName;
            }
        }

        if ($foundDeprecatedSets === []) {
            return;
        }

        $this->reportFoundSets($foundDeprecatedSets);
    }

    /**
     * @param string[] $setNames
     */
    private function reportFoundSets(array $setNames): void
    {
        $symfonyStyleFactory = new SymfonyStyleFactory();
        $symfonyStyle = $symfonyStyleFactory->create();

        foreach ($setNames as $setName) {
            $deprecatedMessage = sprintf(
                'The "%s" set from ECS is outdated and deprecated. Switch to standardized "PSR_12" or include rules manually.',
                $setName
            );

            $symfonyStyle->warning($deprecatedMessage);
        }

        // to make deprecation noticeable
        sleep(3);
    }
}
