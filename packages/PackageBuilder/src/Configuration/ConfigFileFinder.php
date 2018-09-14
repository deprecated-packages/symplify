<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Configuration;

use Nette\Utils\Strings;
use Symfony\Component\Console\Input\InputInterface;
use Symplify\PackageBuilder\Exception\Configuration\FileNotFoundException;
use function Safe\getcwd;
use function Safe\sprintf;

final class ConfigFileFinder
{
    /**
     * @var string[]
     */
    private static $configFilePaths = [];

    public static function detectFromInput(string $name, InputInterface $input): void
    {
        $configValue = self::getOptionValue($input, ['--config', '-c']);
        if ($configValue === null) {
            return;
        }

        $filePath = self::makeAbsolutePath($configValue);

        if (! file_exists($filePath)) {
            throw new FileNotFoundException(sprintf('File "%s" not found in "%s".', $filePath, $configValue));
        }

        self::$configFilePaths[$name] = $filePath;
    }

    /**
     * @param string[] $configNames
     */
    public static function provide(string $name, array $configNames = []): ?string
    {
        if (isset(self::$configFilePaths[$name])) {
            return self::$configFilePaths[$name];
        }

        foreach ($configNames as $configName) {
            $rootConfigPath = getcwd() . DIRECTORY_SEPARATOR . $configName;
            if (is_file($rootConfigPath)) {
                return self::$configFilePaths[$name] = $rootConfigPath;
            }
        }

        return null;
    }

    public static function set(string $name, string $configFilePath): void
    {
        self::$configFilePaths[$name] = $configFilePath;
    }

    public static function makeAbsolutePath(string $relativeFilePath): string
    {
        return Strings::match($relativeFilePath, '#/|\\\\|[a-z]:#iA')
            ? $relativeFilePath
            : getcwd() . DIRECTORY_SEPARATOR . $relativeFilePath;
    }

    /**
     * @param string[] $optionNames
     */
    public static function getOptionValue(InputInterface $input, array $optionNames): ?string
    {
        foreach ($optionNames as $optionName) {
            if ($input->hasParameterOption($optionName)) {
                return $input->getParameterOption($optionName);
            }
        }

        return null;
    }
}
