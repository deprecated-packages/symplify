<?php

declare(strict_types=1);

namespace Symplify\NeonToYamlConverter;

use Nette\Neon\Entity;
use Nette\Neon\Neon;
use Nette\Utils\Strings;
use Symfony\Component\Yaml\Yaml;
use Symplify\NeonToYamlConverter\ConverterWorker\ParameterConverterWorker;
use Symplify\NeonToYamlConverter\ConverterWorker\ServiceConverterWorker;
use Symplify\NeonToYamlConverter\Formatter\YamlOutputFormatter;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\NeonToYamlConverter\Tests\NeonToYamlConverterTest
 */
final class NeonToYamlConverter
{
    /**
     * @var string
     */
    private const SERVICES_KEY = 'services';

    /**
     * @var string
     */
    private const PARAMETERS_KEY = 'parameters';

    /**
     * @var string
     */
    private const INCLUDES_KEY = 'includes';

    /**
     * @var string
     */
    private const IMPORTS_KEY = 'imports';

    /**
     * @see https://regex101.com/r/6ULWEC/1
     * @var string
     */
    private const NULL_REGEX = "#: null\n#";

    /**
     * @see https://regex101.com/r/djToR8/1
     * @var string
     */
    private const TILDA_NULL_QUOTED_REGEX = "#: '~'\n#";

    /**
     * @see https://regex101.com/r/4qSwF3/1/
     * @var string
     */
    private const NEON_SUFFIX_REGEX = '#\.neon$#';

    /**
     * @see https://regex101.com/r/ZFOLCA/1
     * @var string
     */
    private const KERNEL_PROJECT_REGEX = '#%kernel\.project_dir%\/app\/\.\.#';

    /**
     * @see https://regex101.com/r/DjjoEB/1
     * @var string
     */
    private const WWW_DIR_REGEX = '#\%wwwDir\%#';

    /**
     * @see https://regex101.com/r/NrHQiR/1
     * @var string
     */
    private const APP_DIR_REGEX = '#%appDir%#';

    /**
     * @see https://regex101.com/r/2bsgzr/1
     * @var string
     */
    private const ENV_GET_REGEX = "#\@env::get\(\'?(.*?)\'?(,.*?)?\)#ms";

    /**
     * @var ArrayParameterCollector
     */
    private $arrayParameterCollector;

    /**
     * @var YamlOutputFormatter
     */
    private $yamlOutputFormatter;

    /**
     * @var ServiceConverterWorker
     */
    private $serviceConverterWorker;

    /**
     * @var ParameterConverterWorker
     */
    private $parameterConverterWorker;

    public function __construct(
        ArrayParameterCollector $arrayParameterCollector,
        YamlOutputFormatter $yamlOutputFormatter,
        ServiceConverterWorker $serviceConverterWorker,
        ParameterConverterWorker $parameterConverterWorker
    ) {
        $this->arrayParameterCollector = $arrayParameterCollector;
        $this->yamlOutputFormatter = $yamlOutputFormatter;
        $this->serviceConverterWorker = $serviceConverterWorker;
        $this->parameterConverterWorker = $parameterConverterWorker;
    }

    public function convertFileInfo(SmartFileInfo $fileInfo): string
    {
        $content = $fileInfo->getContents();

        $content = $this->convertEnv($content);

        $data = (array) Neon::decode($content);

        foreach ($data as $key => $value) {
            // @traverse deep and subscribe to Entity
            if ($value instanceof Entity) {
                $data[$key] = $this->convertNeonEntityToArray($value);
            }

            if ($key === self::SERVICES_KEY) {
                $data[$key] = $this->serviceConverterWorker->convert((array) $value);
            }

            if ($key === self::PARAMETERS_KEY) {
                $data[$key] = $this->parameterConverterWorker->convert((array) $value);
            }

            if ($key === self::INCLUDES_KEY) {
                unset($data[$key]);
                $importsData = $this->convertIncludes((array) $value);
                $importsContent = Yaml::dump($importsData, 1, 4, Yaml::DUMP_OBJECT_AS_MAP);
                $data[self::IMPORTS_KEY] = $importsContent;
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
        return Strings::replace($content, self::ENV_GET_REGEX, "'%env($1)%'");
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
    private function convertIncludes(array $data): array
    {
        foreach ($data as $key => $value) {
            if (! Strings::contains($value, 'vendor')) {
                $value = Strings::replace($value, self::NEON_SUFFIX_REGEX, '.yaml');
            }

            $data[$key] = [
                'resource' => $value,
            ];
        }

        return $data;
    }

    private function replaceAppDirAndWwwDir(string $content): string
    {
        // @see https://symfony.com/blog/new-in-symfony-3-3-a-simpler-way-to-get-the-project-root-directory
        // %appDir% → %kernel.project_dir%/app
        $content = Strings::replace($content, self::APP_DIR_REGEX, '%kernel.project_dir%/app');

        // %wwwDir% → %kernel.project_dir%/public
        $content = Strings::replace($content, self::WWW_DIR_REGEX, '%kernel.project_dir%/public');

        // %kernel.project_dir%/app/..% → %kernel.project_dir%
        return Strings::replace($content, self::KERNEL_PROJECT_REGEX, '%kernel.project_dir%');
    }

    private function replaceTilda(string $content): string
    {
        $content = Strings::replace($content, self::TILDA_NULL_QUOTED_REGEX, ': ~' . PHP_EOL);

        return Strings::replace($content, self::NULL_REGEX, ': ~' . PHP_EOL);
    }

    private function replaceOldToNewParameters(string $content): string
    {
        foreach ($this->arrayParameterCollector->getParametersToReplace() as $oldParameter => $newParameter) {
            $content = Strings::replace($content, '#' . preg_quote($oldParameter, '#') . '#', $newParameter);
        }

        return $content;
    }
}
