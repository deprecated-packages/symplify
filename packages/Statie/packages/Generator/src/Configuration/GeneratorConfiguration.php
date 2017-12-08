<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Configuration;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\PostFile;

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
        $generators = $this->addPostDefaults($generators);

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

    /**
     * @param mixed[] $generators
     * @return mixed[]
     */
    private function addPostDefaults(array $generators): array
    {
        if (isset($generators['posts'])) {
            return $generators;
        }

        $generators['posts'] = [
            # name of variable inside one elg, elenent (e,post)
            'variable' => 'post',
            # used global variable with all found items
            'variable_global' => 'posts',
            # directory, where to look for them
            'path' => '_posts',
            # which layout to use, a file from "_layouts/<name>.latte"
            'layout' => 'post',
            # and url prefix, e.g. /blog/some-post.md
            'route_prefix' => 'blog',
            # an object that will wrap it's logic, you can add helper methods into it and use it in templates
            'object' => PostFile::class,
        ];

        return $generators;
    }
}
