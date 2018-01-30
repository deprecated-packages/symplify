<?php declare(strict_types=1);

namespace Symplify\Monorepo\Configuration;

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
            'monorepo.yml'
        ));
    }
}
