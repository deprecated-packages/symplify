<?php declare(strict_types=1);

namespace Symplify\Statie\Configuration;

use Symfony\Component\Console\Input\InputInterface;

final class ConfigFilePathHelper
{
    /**
     * @var string
     */
    private static $configFilePath;

    public static function detectFromInput(InputInterface $input): void
    {
        if ($input->hasParameterOption('--configuration') || $input->hasParameterOption('-c')) {
            $filePath = getcwd() . '/' . $input->getParameterOption('-c');
            if (file_exists($filePath)) {
                self::$configFilePath = $filePath;
            }

            $filePath = getcwd() . '/' . $input->getParameterOption('--configuration');
            if (file_exists($filePath)) {
                self::$configFilePath = $filePath;
            }
        }
    }

    public static function provide(): string
    {
        if (self::$configFilePath) {
            return self::$configFilePath;
        }

        if (file_exists(getcwd() . '/statie.neon')) {
            return self::$configFilePath = getcwd() . '/statie.neon';
        }
    }

    public static function set(string $configFilePath): void
    {
        self::$configFilePath = $configFilePath;
    }
}
