<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Nette\Neon\Exception;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Exception\Neon\InvalidNeonSyntaxException;
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
     * @var NeonParser
     */
    private $neonParser;

    public function __construct(NeonParser $neonParser)
    {
        $this->neonParser = $neonParser;
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
        if (preg_match(self::CONFIG_AND_CONTENT_PATTERN, $file->getContent(), $matches)) {
            $file->changeContent($matches['content']);
            if ($matches['config']) {
                $this->setConfigurationToFileIfFoundAny($matches['config'], $file);
            }
        }
    }

    private function setConfigurationToFileIfFoundAny(string $content, AbstractFile $file): void
    {
        try {
            $configuration = $this->neonParser->decode($content);
        } catch (Exception $neonException) {
            throw new InvalidNeonSyntaxException(sprintf(
                'Invalid NEON syntax found in "%s" file: %s',
                $file->getFilePath(),
                $neonException->getMessage()
            ));
        }

        $file->addConfiguration($configuration);
    }
}
