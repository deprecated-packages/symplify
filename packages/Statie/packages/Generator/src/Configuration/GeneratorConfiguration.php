<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Configuration;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Statie\Configuration\Configuration;

final class GeneratorConfiguration
{
    /**
     * @var string
     */
    private const CONFIG_KEY = 'generators';

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var GeneratorElement[]
     */
    private $generatorElements = [];

    public function __construct(ParameterProvider $parameterProvider, Configuration $configuration)
    {
        $this->parameterProvider = $parameterProvider;
        $this->configuration = $configuration;
    }

    /**
     * @return GeneratorElement[]
     */
    public function getGeneratorElements(): array
    {
        if ($this->generatorElements) {
            return $this->generatorElements;
        }

        $generators = (array) $this->parameterProvider->provideParameter(self::CONFIG_KEY);

        $generatorElements = [];

        foreach ($generators as $key => $generatorConfiguration) {
            GeneratorElementGuard::ensureInputIsValid($key, $generatorConfiguration);
            // make path absolute
            $generatorConfiguration = $this->makePathAbsolute($generatorConfiguration);
            $generatorElements[] = GeneratorElement::createFromConfiguration($generatorConfiguration);
        }

        return $this->generatorElements = $generatorElements;
    }

    /**
     * @param mixed[] $generatorConfiguration
     * @return mixed[]
     */
    private function makePathAbsolute(array $generatorConfiguration): array
    {
        $generatorConfiguration['path'] = realpath($this->configuration->getSourceDirectory()) .
            DIRECTORY_SEPARATOR .
            $generatorConfiguration['path'];

        return $generatorConfiguration;
    }
}
