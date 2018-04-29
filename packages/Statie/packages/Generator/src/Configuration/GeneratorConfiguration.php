<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Configuration;

use Symplify\PackageBuilder\Parameter\ParameterProvider;

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
     * @var GeneratorElement[]
     */
    private $generatorElements = [];

    /**
     * @var GeneratorElementFactory
     */
    private $generatorElementFactory;

    public function __construct(
        ParameterProvider $parameterProvider,
        GeneratorElementFactory $generatorElementFactory
    ) {
        $this->parameterProvider = $parameterProvider;
        $this->generatorElementFactory = $generatorElementFactory;
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
            $generatorElements[] = $this->generatorElementFactory->createFromConfiguration(
                $key,
                $generatorConfiguration
            );
        }

        return $this->generatorElements = $generatorElements;
    }
}
