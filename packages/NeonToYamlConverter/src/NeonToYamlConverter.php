<?php declare(strict_types=1);

namespace Symplify\NeonToYamlConverter;

use Nette\Neon\Entity;
use Nette\Neon\Neon;
use Nette\Utils\Strings;
use Symfony\Component\Yaml\Yaml;
use Symplify\NeonToYamlConverter\Formatter\YamlOutputFormatter;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class NeonToYamlConverter
{
    /**
     * @todo maybe use to dump env vars
     * @var string[]
     */
    private $environmentVariables = [];

    /**
     * @var ArrayParameterCollector
     */
    private $arrayParameterCollector;

    /**
     * @var YamlOutputFormatter
     */
    private $yamlOutputFormatter;

    public function __construct(
        ArrayParameterCollector $arrayParameterCollector,
        YamlOutputFormatter $yamlOutputFormatter
    ) {
        $this->arrayParameterCollector = $arrayParameterCollector;
        $this->yamlOutputFormatter = $yamlOutputFormatter;
    }

    public function convertFile(SmartFileInfo $fileInfo): string
    {
        $content = $fileInfo->getContents();

        $content = $this->convertEnv($content);

        $data = (array) Neon::decode($content);

        foreach ($data as $key => $value) {
            // @traverse deep and subscribe to Entity
            if ($value instanceof Entity) {
                $data[$key] = $this->convertNeonEntityToArray($value);
            }

            if ($key === 'services') {
                $data[$key] = $this->convertServices((array) $value);
            }

            if ($key === 'parameters') {
                $data[$key] = $this->convertParameters((array) $value);
            }

            if ($key === 'includes') {
                unset($data[$key]);
                $importsData = $this->convertIncludes((array) $value);
                $importsContent = Yaml::dump($importsData, 1, 4, Yaml::DUMP_OBJECT_AS_MAP);
                $data['imports'] = $importsContent;
            }
        }

        $content = Yaml::dump($data, 100, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_OBJECT);

        $content = $this->replaceAppDirAndWwwDir($content);
        $content = $this->replaceTilda($content);

        $content = $this->yamlOutputFormatter->format($content);

        return $this->replaceOldToNewParameters($content);
    }

    private function convertEnv(string $content): string
    {
        // https://regex101.com/r/IxBjFD/1
        return Strings::replace($content, "#\@env::get\(\'?(.*?)\'?(,.*?)?\)#ms", "'%env($1)%'");
    }

    /**
     * @return mixed[]
     */
    private function convertNeonEntityToArray(Entity $entity): array
    {
        return array_merge([
            'value' => $entity->value,

        ], $entity->attributes);
    }

    /**
     * @param mixed[] $data
     * @return mixed[]
     */
    private function convertServices(array $data): array
    {
        foreach ($data as $name => $service) {
            if (is_int($name)) { // not named
                if (is_string($service)) { // just single-class
                    unset($data[$name]);
                    $name = $service;
                    $data[$name] = null;
                }

                if ($service instanceof Entity) {
                    [
                     $name, $data,
                    ] = $this->convertServiceEntity($data, $service, $name);
                }
            } elseif ($service instanceof Entity) {
                [
                 $name, $data,
                ] = $this->convertServiceEntity($data, $service, $name);
            } elseif (is_string($service)) {
                if (is_string($name) && $service === '~') {
                    $data[$name] = null;
                    continue;
                }

                // probably factory, @see https://symfony.com/doc/current/service_container/factories.html
                if (Strings::contains($service, '::')) {
                    [
                     $factoryClass, $factoryMethod,
                    ] = explode('::', $service);

                    $data[$name] = [
                        'factory' => [$factoryClass, $factoryMethod],
                    ];
                // probably alias, @see https://symfony.com/doc/current/service_container/alias_private.html#aliasing
                } elseif (Strings::startsWith($service, '@')) {
                    $data[$name] = ['alias' => $service];
                // probably service
                } else {
                    $data[$name] = ['class' => $service];
                }
            } else { // named service
                $service = $data[$name];
                if (isset($service['class'])) {
                    if ($service['class'] instanceof Entity) {
                        if ($service['class']->attributes) {
                            $service['arguments'] = $service['class']->attributes;
                        }
                        $service['class'] = $service['class']->value;
                    }
                }

                $data[$name] = $service;
            }

            $service = $data[$name];
            if (isset($service['setup'])) {
                foreach ((array) $service['setup'] as $key => $value) {
                    if ($value instanceof Entity) {
                        $service['setup'][$key] = [$value->value, $value->attributes];
                    }
                }

                // inline calls - requires fixup in YamlOutputFormatter
                $setupYamlContent = Yaml::dump($service['setup'], 1, 4, Yaml::DUMP_OBJECT);
                $service['calls'] = $setupYamlContent;
                unset($service['setup']);

                $data[$name] = $service;
            }

            $service = $data[$name];
            if (isset($service['arguments'])) {
                foreach ((array) $service['arguments'] as $key => $value) {
                    if ($value instanceof Entity) {
                        if ($value->value === '@env::get') { // enviro value! @see https://symfony.com/blog/new-in-symfony-3-4-advanced-environment-variables
                            $environmentVariable = $value->attributes[0];
                            $this->environmentVariables[] = $environmentVariable;
                            $service['arguments'][$key] = sprintf('%%env(%s)%%', $environmentVariable);
                        }
                    }
                }
            }

            $data[$name] = $service;
        }

        return $data;
    }

    /**
     * @param mixed[] $data
     * @return mixed[]
     */
    private function convertParameters(array $data): array
    {
        foreach ($data as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            foreach ($value as $key2 => $value2) {
                $oldKey = $key . '.' . $key2;

                $newKey = $this->arrayParameterCollector->matchParameterToReplace($oldKey);
                if ($newKey === null) {
                    continue;
                }

                // replace key
                unset($data[$key][$key2]);
                $data[$newKey] = $value2;
            }

            if ($data[$key] === []) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * @param mixed[] $data
     * @return mixed[]
     */
    private function convertIncludes(array $data): array
    {
        foreach ($data as $key => $value) {
            if (Strings::contains($value, 'vendor') === false) {
                $value = Strings::replace($value, '#\.neon$#', '.yaml');
            }

            $data[$key] = ['resource' => $value];
        }

        return $data;
    }

    private function replaceAppDirAndWwwDir(string $content): string
    {
        // @see https://symfony.com/blog/new-in-symfony-3-3-a-simpler-way-to-get-the-project-root-directory
        // %appDir% → %kernel.project_dir%/app
        $content = Strings::replace($content, '#%appDir%#', '%kernel.project_dir%/app');

        // %wwwDir% → %kernel.project_dir%/public
        $content = Strings::replace($content, '#%wwwDir%#', '%kernel.project_dir%/public');

        // %kernel.project_dir%/app/..% → %kernel.project_dir%
        return Strings::replace($content, '#%kernel.project_dir%\/app\/\.\.#', '%kernel.project_dir%');
    }

    private function replaceTilda(string $content): string
    {
        $content = Strings::replace($content, "#: '~'\n#", ': ~' . PHP_EOL);

        return Strings::replace($content, "#: null\n#", ': ~' . PHP_EOL);
    }

    private function replaceOldToNewParameters(string $content): string
    {
        foreach ($this->arrayParameterCollector->getParametersToReplace() as $oldParameter => $newParamter) {
            $content = Strings::replace($content, '#' . preg_quote($oldParameter) . '#', $newParamter);
        }

        return $content;
    }

    /**
     * @param mixed[] $data
     * @param string|int $name
     * @return mixed[]
     */
    private function convertServiceEntity(array $data, Entity $entity, $name): array
    {
        $class = $entity->value;
        $serviceData = [
            'class' => $class,
            'arguments' => $entity->attributes,
        ];

        if (is_int($name)) { // class-named service
            // is namespaced class?
            if (Strings::contains($serviceData['class'], '\\')) {
                unset($serviceData['class']);
            }

            unset($data[$name]);
            $name = $class;
        }

        $data[$name] = $serviceData;

        return [$name, $data];
    }
}
