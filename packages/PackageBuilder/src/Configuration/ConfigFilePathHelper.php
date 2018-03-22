<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Configuration;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\PackageBuilder\Exception\Configuration\FileNotFoundException;

final class ConfigFilePathHelper
{
    /**
     * @var string
     */
    private const CONFIG_OPTION_NAME = '--config';

    /**
     * @var string
     */
    private const SHORT_CONFIG_OPTION_NAME = '-c';

    /**
     * @var string[]
     */
    private static $configFilePaths = [];

    public static function detectFromInput(string $name, InputInterface $input): void
    {
        $configValue = self::getConfigValue($input);
        if ($configValue === null) {
            return;
        }

        $filePath = self::makeAbsolutePath($configValue);

        if (! file_exists($filePath)) {
            throw new FileNotFoundException(sprintf(
                'File "%s" not found in "%s".',
                $filePath,
                $configValue
            ));
        }

        self::$configFilePaths[$name] = $filePath;
    }

    public static function provide(string $name, ?string $configName = null): ?string
    {
        if (isset(self::$configFilePaths[$name])) {
            return self::$configFilePaths[$name];
        }

        $rootConfigPath = getcwd() . DIRECTORY_SEPARATOR . $configName;
        if (is_file($rootConfigPath)) {
            return self::$configFilePaths[$name] = $rootConfigPath;
        }

        return null;
    }

    public static function set(string $name, string $configFilePath): void
    {
        self::$configFilePaths[$name] = $configFilePath;
    }

    public static function makeAbsolutePath(string $relativeFilePath): string
    {
        return preg_match('#/|\\\\|[a-z]:#iA', $relativeFilePath)
            ? $relativeFilePath
            : getcwd() . DIRECTORY_SEPARATOR . $relativeFilePath;
    }

    private static function getConfigValue(InputInterface $input): ?string
    {
        if ($input->hasParameterOption(self::CONFIG_OPTION_NAME)) {
            return $input->getParameterOption(self::CONFIG_OPTION_NAME);
        }

        if ($input->hasParameterOption(self::SHORT_CONFIG_OPTION_NAME)) {
            return $input->getParameterOption(self::SHORT_CONFIG_OPTION_NAME);
        }

        return null;
    }
}
