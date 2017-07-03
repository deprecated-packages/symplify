<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Configuration;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symplify\PackageBuilder\Exception\Configuration\FileNotFoundException;

final class ConfigFilePathHelper
{
    /**
     * @var string
     */
    private const CONFIG_OPTION_NAME = 'config';

    /**
     * @var string[][]
     */
    private static $configFilePaths = [];

    public static function detectFromInput(string $name, InputInterface $input): void
    {
        $inputDefinition = self::createInputDefinition();
        $input->bind($inputDefinition);

        if ($input->hasOption(self::CONFIG_OPTION_NAME)) {
            $relativeFilePaths = $input->getOption(self::CONFIG_OPTION_NAME);

            $filePaths = [];
            foreach ($relativeFilePaths as $relativeFilePath) {
                $filePaths[] = self::detectFilePath($relativeFilePath);
            }

            self::$configFilePaths[$name] = $filePaths;
        }
    }

    public static function provide(string $name, ?string $configName = null): ?array
    {
        if (isset(self::$configFilePaths[$name])) {
            return self::$configFilePaths[$name];
        }

        $rootConfigPath = getcwd() . '/' . $configName;
        if (file_exists($rootConfigPath)) {
            self::$configFilePaths[$name][] = $rootConfigPath;
            return self::$configFilePaths[$name];
        }

        return null;
    }

    public static function set(string $name, string $configFilePath): void
    {
        self::$configFilePaths[$name] = $configFilePath;
    }

    private static function createInputDefinition(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption(
                self::CONFIG_OPTION_NAME, NULL,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED
            )
        ]);
    }

    private static function detectFilePath(string $relativeFilePath): string
    {
        $filePath = getcwd() . '/' . $relativeFilePath;

        if (! file_exists($filePath)) {
            throw new FileNotFoundException(sprintf(
                'File "%s" not found in "%s".',
                $filePath,
                $relativeFilePath
            ));
        }

        return $filePath;
    }
}
