<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Nette\Utils\Strings;
use ParsedownExtra;
use Symplify\Statie\Configuration\Parser\YamlParser;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Symplify\Statie\Renderable\File\AbstractFile;

final class ConfigurationFileDecorator implements FileDecoratorInterface
{
    /**
     * @var string
     */
    private const SLASHES_WITH_SPACES_PATTERN = '(?:---[\s]*[\r\n]+)';

    /**
     * @var YamlParser
     */
    private $yamlParser;

    /**
     * @var ParsedownExtra
     */
    private $parsedownExtra;

    public function __construct(YamlParser $yamlParser, ParsedownExtra $parsedownExtra)
    {
        $this->yamlParser = $yamlParser;
        $this->parsedownExtra = $parsedownExtra;
    }

    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFiles(array $files): array
    {
        foreach ($files as $file) {
            $this->decorateFile($file);
        }

        return $files;
    }

    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFilesWithGeneratorElement(array $files, GeneratorElement $generatorElement): array
    {
        return $this->decorateFiles($files);
    }

    /**
     * Has to run before Markdown; so it can render perex and content is set.
     */
    public function getPriority(): int
    {
        return 1000;
    }

    private function decorateFile(AbstractFile $file): void
    {
        $matches = Strings::match($file->getContent(), $this->getConfigAndContentPattern());

        if ($matches) {
            $file->changeContent($matches['content']);

            if ($file instanceof AbstractGeneratorFile) {
                // create text-only content - without html, without configuration, without markdown
                $htmlContent = $this->parsedownExtra->parse($matches['content']);
                $rawContent = strip_tags($htmlContent);
                $file->setRawContent($rawContent);
            }

            if ($matches['config']) {
                $this->setConfigurationToFileIfFoundAny($matches['config'], $file);
            }
        }
    }

    private function getConfigAndContentPattern(): string
    {
        return sprintf(
            '#^\s*%s?(?<config>.*?)%s(?<content>.*?)$#s',
            self::SLASHES_WITH_SPACES_PATTERN,
            self::SLASHES_WITH_SPACES_PATTERN
        );
    }

    private function setConfigurationToFileIfFoundAny(string $content, AbstractFile $file): void
    {
        $configuration = $this->yamlParser->decodeInSource($content, $file->getFilePath());
        $file->addConfiguration($configuration);
    }
}
