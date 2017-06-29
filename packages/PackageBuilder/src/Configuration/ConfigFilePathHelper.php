<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Configuration;

use Symfony\Component\Console\Input\ArgvInput;

final class ConfigFilePathHelper
{
    /**
     * @var string
     */
    private const SHORT_C = '-c';

    /**
     * @var string
     */
    private const LONG_CONFIGURATION = '--configuration';

    /**
     * @var string[]
     */
    private static $configFilePaths = [];

    public static function detectFromInput(string $name): void
    {
        $input = new ArgvInput;

        if ($input->hasParameterOption(self::LONG_CONFIGURATION)) {
            $filePath = getcwd() . '/' . $input->getParameterOption(self::LONG_CONFIGURATION);
            if (file_exists($filePath)) {
                self::$configFilePaths[$name] = $filePath;
            }
        }

        if ($input->hasParameterOption(self::SHORT_C)) {
            $filePath = getcwd() . '/' . $input->getParameterOption(self::SHORT_C);
            if (file_exists($filePath)) {
                self::$configFilePaths[$name] = $filePath;
            }
        }
    }

    public static function provide(string $name): string
    {
        if (isset(self::$configFilePaths[$name])) {
            return self::$configFilePaths[$name];
        }

        $rootConfigPath = getcwd() . '/' . $name . '.neon';
        if (file_exists($rootConfigPath)) {
            return self::$configFilePaths[$name] = $rootConfigPath;
        }
    }

    public static function set(string $name, string $configFilePath): void
    {
        self::$configFilePaths[$name] = $configFilePath;
    }
}
