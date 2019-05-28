<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Parameter;

use Nette\Utils\RegexpException;
use Nette\Utils\Strings;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\PackageBuilder\Exception\Parameter\ParameterTypoException;

final class ParameterTypoProofreader
{
    /**
     * @var mixed[]
     */
    private $correctToTypos = [];

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @param Container $container
     * @param mixed[] $correctToTypos
     */
    public function __construct(array $correctToTypos, ContainerInterface $container)
    {
        $this->parameterBag = $container->getParameterBag();
        $this->correctToTypos = $correctToTypos;
    }

    public function process(): void
    {
        $parameters = $this->parameterBag->all();

        $parameterNames = array_keys($parameters);
        $parameterNames = $this->filterOutSystemParameterNames($parameterNames);

        $correctNames = array_keys($this->correctToTypos);
        foreach ($parameterNames as $parameterName) {
            foreach ($this->correctToTypos as $correctParameterName => $missplacedNames) {
                if ($parameterName === $correctParameterName) {
                    continue;
                }

                if (in_array($parameterName, $correctNames, true)) {
                    continue;
                }

                $this->processParameter($missplacedNames, $parameterName, $correctParameterName);
            }
        }
    }

    /**
     * @param string[] $parameterNames
     * @return string[]
     */
    private function filterOutSystemParameterNames(array $parameterNames): array
    {
        return array_filter($parameterNames, function ($parameterName): bool {
            return ! (bool) Strings::match($parameterName, '#^(kernel|container)\.#');
        });
    }

    /**
     * @param string[] $missplacedNames
     */
    private function processParameter(array $missplacedNames, string $parameterName, string $correctParameterName): void
    {
        foreach ($missplacedNames as $missplacedName) {
            try {
                if (Strings::match($parameterName, $missplacedName)) {
                    $this->throwException($parameterName, $correctParameterName);
                }
            } catch (RegexpException $regexpException) {
                // is not a regex
            }

            if ($parameterName === $missplacedName) {
                $this->throwException($parameterName, $correctParameterName);
            }
        }
    }

    private function throwException(string $providedParameterName, string $correctParameterName): void
    {
        throw new ParameterTypoException(sprintf(
            'Parameter "parameters > %s" does not exist.%sUse "parameters > %s" instead.',
            $providedParameterName,
            PHP_EOL,
            $correctParameterName
        ));
    }
}
