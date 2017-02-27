<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\Configuration;

use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Renderable\File\AbstractFile;

final class ConfigurationDecorator
{
    /**
     * @var NeonParser
     */
    private $neonParser;

    public function __construct(NeonParser $neonParser)
    {
        $this->neonParser = $neonParser;
    }

    public function decorateFile(AbstractFile $file): void
    {
        if (preg_match('/^\s*(?:---[\s]*[\r\n]+)(.*?)(?:---[\s]*[\r\n]+)(.*?)$/s', $file->getContent(), $matches)) {
            $file->changeContent($matches[2]);

            $this->setConfigurationToFileIfFoundAny($matches[1], $file);
        }
    }

    private function setConfigurationToFileIfFoundAny(string $content, AbstractFile $file): void
    {
        if (! preg_match('/^(\s*[-]+\s*|\s*)$/', $content)) {
            $configuration = $this->neonParser->decode($content);
            $file->setConfiguration($configuration);
        }
    }
}
