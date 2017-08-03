<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\Markdown;

use Nette\Utils\Strings;
use ParsedownExtra;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class MarkdownFileDecorator implements FileDecoratorInterface
{
    /**
     * @var ParsedownExtra
     */
    private $parsedownExtra;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(ParsedownExtra $parsedownExtra, Configuration $configuration)
    {
        $this->parsedownExtra = $parsedownExtra;
        $this->configuration = $configuration;
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

    public function getPriority(): int
    {
        // have to run before Latte; it fails the other way
        return 800;
    }

    private function decorateFile(AbstractFile $file): void
    {
        // skip due to HTML content incompatibility
        if ($file->getExtension() !== 'md') {
            return;
        }

        $htmlContent = $this->parsedownExtra->parse($file->getContent());

        if ($this->configuration->isMarkdownHeadlineAnchors()) {
            $htmlContent = $this->decorateHeadlinesWithTocAnchors($htmlContent);
        }

        $file->changeContent($htmlContent);
    }

    private function decorateHeadlinesWithTocAnchors(string $htmlContent): string
    {
        return Strings::replace($htmlContent, '#<h([1-6])>(.*?)<\/h[1-6]>#', function ($result) {
            [$original, $headlineLevel, $headline] = $result;
            $headlineId = Strings::webalize($headline);

            return sprintf(
                '<h%s id="%s"><a class="anchor" href="#%s" aria-hidden="true">'
                        . '<span class="anchor-icon">#</span>'
                        . '</a>%s</h%s>',
                $headlineLevel,
                $headlineId,
                $headlineId,
                $headline,
                $headlineLevel
            );
        });
    }
}
