<?php declare(strict_types=1);

namespace Symplify\Monorepo\Configuration;

use Symplify\Monorepo\Exception\MissingConfigurationSectionException;
use Symplify\Monorepo\Exception\NonEmptyDirectoryException;

final class ConfigurationGuard
{
    /**
     * @param mixed $config
     */
    public function ensureConfigSectionIsFilled($config, string $section): void
    {
        if ($config) {
            return;
        }

        throw new MissingConfigurationSectionException(sprintf(
            'Section "%s" in config is required. Complete it to "%s" file under "parameters"',
            $section,
            ConfigurationOptions::MONOREPO_CONFIG_FILE
        ));
    }

    public function ensureDirectoryIsEmpty(string $directory): void
    {
        if (! file_exists($directory)) {
            return;
        }

        if (count(glob($directory . '/*')) === 0) {
            return;
        }

        throw new NonEmptyDirectoryException(sprintf('Directory "%s" must be empty.', $directory));
    }
}
