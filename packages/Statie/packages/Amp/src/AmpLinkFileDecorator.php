<?php declare(strict_types=1);

namespace Symplify\Statie\Amp;

use Nette\Utils\Strings;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class AmpLinkFileDecorator implements FileDecoratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var HtmlToAmpConvertor
     */
    private $htmlToAmpConvertor;

    public function __construct(Configuration $configuration, HtmlToAmpConvertor $htmlToAmpConvertor)
    {
        $this->configuration = $configuration;
        $this->htmlToAmpConvertor = $htmlToAmpConvertor;
    }

    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFiles(array $files): array
    {
        if (! $this->configuration->isAmpEnabled()) {
            return $files;
        }

        foreach ($files as $file) {
            if (! Strings::endsWith($file->getOutputPath(), '.html')) {
                continue;
            }

            // original file
            $ampFile = clone $file;
            $this->addAmphtmlLinkToFile($file);

            // cloned file
            $baseUrl = $this->configuration->getOptions()['baseUrl'] ?? '';
            $originalUrl = $baseUrl . $file->getOutputPath();

            $amp = $this->htmlToAmpConvertor->convert($ampFile->getContent(), $originalUrl);
            $ampFile->changeContent($amp);

            $ampFile->setOutputPath('/amp/' . $file->getOutputPath());
            $files[] = $ampFile;
        }

        return $files;
    }

    /**
     * Higher priorities are executed first.
     */
    public function getPriority(): int
    {
        return 600;
    }

    private function addAmphtmlLinkToFile(AbstractFile $file): void
    {
        $baseUrl = $this->configuration->getOptions()['baseUrl'] ?? '';
        $ampUrl = $baseUrl . '/amp/' . ltrim($file->getOutputPath(), '/');
        $ampLink = sprintf('<link rel="amphtml" href="%s">', $ampUrl);

        $content = $file->getContent();
        if (Strings::contains($content, '</head>')) {
            $content = str_replace('</head>', $ampLink . PHP_EOL . '</head>', $content);
        } else {
            $content = $ampLink . PHP_EOL . $content;
        }

        $file->changeContent($content);
    }
}
