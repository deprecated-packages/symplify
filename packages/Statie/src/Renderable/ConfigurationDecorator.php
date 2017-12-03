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

    private function decorateFile(AbstractFile $file): void
    {
        if (preg_match('/^\s*(?:---[\s]*[\r\n]+)(.*?)(?:---[\s]*[\r\n]+)(.*?)$/s', $file->getContent(), $matches)) {
            $file->changeContent($matches[2]);
            $this->setConfigurationToFileIfFoundAny($matches[1], $file);
        }
    }

    private function setConfigurationToFileIfFoundAny(string $content, AbstractFile $file): void
    {
        if (! preg_match('/^(\s*[-]+\s*|\s*)$/', $content)) {
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

    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFilesWithGeneratorElement(array $files, GeneratorElement $generatorElement): array
    {
        // TODO: Implement decorateFilesWithGeneratorElement() method.
    }
}
