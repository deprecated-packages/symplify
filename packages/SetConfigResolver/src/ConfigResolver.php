<?php declare(strict_types=1);

namespace Symplify\SetConfigResolver;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\SetConfigResolver\Console\Option\OptionName;
use Symplify\SetConfigResolver\Console\OptionValueResolver;
use Symplify\SetConfigResolver\Yaml\YamlSetsResolver;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConfigResolver
{
    /**
     * @var string|null
     */
    private $firstResolverConfig;

    /**
     * @var YamlSetsResolver
     */
    private $yamlSetsResolver;

    /**
     * @var SetResolver
     */
    private $setResolver;

    /**
     * @var OptionValueResolver
     */
    private $optionValueResolver;

    public function __construct()
    {
        $this->setResolver = new SetResolver();
        $this->yamlSetsResolver = new YamlSetsResolver();
        $this->optionValueResolver = new OptionValueResolver();
    }

    /**
     * @param string[] $fallbackFiles
     */
    public function resolveFromInputWithFallback(InputInterface $input, array $fallbackFiles = []): ?string
    {
        $configValue = $this->optionValueResolver->getOptionValue($input, OptionName::CONFIG);
        if ($configValue !== null) {
            if (! file_exists($configValue)) {
                throw new FileNotFoundException(sprintf('File "%s" was not found', $configValue));
            }

            $this->firstResolverConfig = $configValue;

            $smartFileInfo = new SmartFileInfo($configValue);
            return $smartFileInfo->getRealPath();
        }

        foreach ($fallbackFiles as $fallbackFile) {
            $rootFallbackFile = getcwd() . DIRECTORY_SEPARATOR . $fallbackFile;
            if (is_file($rootFallbackFile)) {
                $this->firstResolverConfig = $rootFallbackFile;

                return $rootFallbackFile;
            }
        }

        return null;
    }

    /**
     * @param string[] $configFiles
     * @return string[]
     */
    public function resolveFromParameterSetsFromConfigFiles(array $configFiles, string $setDirectory): array
    {
        $sets = $this->yamlSetsResolver->resolveFromConfigFiles($configFiles);

        return $this->resolveFromSets($sets, $setDirectory);
    }

    public function getFirstResolvedConfig(): ?string
    {
        return $this->firstResolverConfig;
    }

    public function resolveSetFromInputAndDirectory(InputInterface $input, string $configDirectory): ?string
    {
        return $this->setResolver->detectFromInputAndDirectory($input, $configDirectory);
    }

    /**
     * @param string[] $sets
     * @return string[]
     */
    private function resolveFromSets(array $sets, string $setDirectory): array
    {
        $configs = [];
        foreach ($sets as $set) {
            $configs[] = $this->setResolver->detectFromNameAndDirectory($set, $setDirectory);
        }

        return $configs;
    }
}
