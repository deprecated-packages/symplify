<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Yaml\FileLoader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symplify\PackageBuilder\Yaml\ParameterInImportResolver;

abstract class AbstractParameterImportsYamlFileLoader extends YamlFileLoader
{
    /**
     * @var ParameterInImportResolver
     */
    private $parameterInImportResolver;

    public function __construct(ContainerBuilder $containerBuilder, FileLocatorInterface $fileLocator)
    {
        $this->parameterInImportResolver = new ParameterInImportResolver();

        parent::__construct($containerBuilder, $fileLocator);
    }

    /**
     * @param string $file
     * @return array|mixed|mixed[]
     */
    protected function loadFile($file)
    {
        /** @var mixed[]|null $configuration */
        $configuration = parent::loadFile($file);
        if ($configuration === null) {
            return [];
        }

        return $this->parameterInImportResolver->process($configuration);
    }
}
