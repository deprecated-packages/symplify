<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Configuration;

use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class GeneratorConfiguration
{
    const CONFIG_KEY = 'generators';
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }

    /**
     * @return GeneratorElement[]
     */
    public function getGeneratorElements(): array
    {
        dump($this->parameterProvider);

        $generators = $this->parameterProvider->provideParameter(self::CONFIG_KEY);
        dump($generators);
        die;
    }
}
