<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Parser\ParserException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symplify\Statie\Configuration\Parser\YamlParser;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Exception\Yaml\InvalidYamlSyntaxException;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Renderable\File\AbstractFile;

final class ConfigurationDecorator implements FileDecoratorInterface
{
    /**
     * @var string
     */
    private const CONFIG_AND_CONTENT_PATTERN =
        '/^\s*' .
        self::SLASHES_WITH_SPACES_PATTERN . '(?<config>.*?)' . self::SLASHES_WITH_SPACES_PATTERN .
        '(?<content>.*?)$/s';

    /**
     * @var string
     */
    private const SLASHES_WITH_SPACES_PATTERN = '(?:---[\s]*[\r\n]+)';

    /**
     * @var YamlParser
     */
    private $yamlParser;

    public function __construct(YamlParser $yamlParser)
    {
        $this->yamlParser = $yamlParser;
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

    private function decorateFile(AbstractFile $file): void
    {
        $matches = Strings::match($file->getContent(), self::CONFIG_AND_CONTENT_PATTERN);
        if ($matches) {
            $file->changeContent($matches['content']);
            if ($matches['config']) {
                $this->setConfigurationToFileIfFoundAny($matches['config'], $file);
            }
        }
    }

    private function setConfigurationToFileIfFoundAny(string $content, AbstractFile $file): void
    {
        try {
            $configuration = $this->yamlParser->decode($content);
        } catch (ParseException $parseException) {
            throw new InvalidYamlSyntaxException(sprintf(
                'Invalid YAML syntax found in "%s" file: %s',
                $file->getFilePath(),
                $parseException->getMessage()
            ));
        }

        $file->addConfiguration($configuration);
    }
}
