<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Adapter\Nette\Parameter;

use Nette\DI\Container;

final class ParameterProvider
{
    /**
     * @var mixed[]
     */
    private $parameters = [];

    public function __construct(Container $container)
    {
        $parameters = $container->getParameters();

        $this->parameters = $this->unsetSystemParameters($parameters);
    }

    /**
     * @return mixed[]
     */
    public function provide(): array
    {
        return $this->parameters;
    }

    /**
     * @param mixed[] $parameters
     * @return mixed[]
     */
    private function unsetSystemParameters(array $parameters): array
    {
        $systemValues = ['appDir', 'wwwDir', 'debugMode', 'productionMode', 'consoleMode', 'tempDir'];
        foreach ($parameters as $name => $value) {
            if (in_array($name, $systemValues, true)) {
                unset($parameters[$name]);
            }
        }

        return $parameters;
    }
}
