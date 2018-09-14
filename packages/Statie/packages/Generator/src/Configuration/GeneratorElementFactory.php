<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Configuration;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Generator\FileNameObjectSorter;
use Symplify\Statie\Generator\Renderable\File\GeneratorFile;
use function Safe\realpath;

final class GeneratorElementFactory
{
    /**
     * @var GeneratorElementGuard
     */
    private $generatorElementGuard;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(GeneratorElementGuard $generatorElementGuard, Configuration $configuration)
    {
        $this->generatorElementGuard = $generatorElementGuard;
        $this->configuration = $configuration;
    }

    /**
     * @param mixed[] $configuration
     */
    public function createFromConfiguration(string $name, array $configuration): GeneratorElement
    {
        $this->generatorElementGuard->ensureInputIsValid($name, $configuration);

        $configuration = $this->makePathAbsolute($configuration);

        return new GeneratorElement(
            $configuration['variable'],
            $configuration['variable_global'],
            $configuration['path'],
            $configuration['layout'],
            $configuration['route_prefix'],
            $configuration['object'] ?? GeneratorFile::class,
            isset($configuration['object_sorter']) ? new $configuration['object_sorter']() : new FileNameObjectSorter()
        );
    }

    /**
     * @param mixed[] $configuration
     * @return mixed[]
     */
    private function makePathAbsolute(array $configuration): array
    {
        $configuration['path'] = realpath($this->configuration->getSourceDirectory()) .
            DIRECTORY_SEPARATOR .
            $configuration['path'];

        return $configuration;
    }
}
