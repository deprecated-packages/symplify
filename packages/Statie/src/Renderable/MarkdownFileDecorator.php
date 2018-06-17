<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Nette\Utils\Strings;
use ParsedownExtra;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
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

    /**
     * @param AbstractFile[] $files
     * @return AbstractFile[]
     */
    public function decorateFilesWithGeneratorElement(array $files, GeneratorElement $generatorElement): array
    {
        return $this->decorateFiles($files);
    }

    /**
     * Higher priorities are executed first.
     *
     * Has to run before Latte; it fails the other way.
     */
    public function getPriority(): int
    {
        return 800;
    }

    private function decorateFile(AbstractFile $file): void
    {
        // skip due to HTML content incompatibility
        if ($file->getExtension() !== 'md') {
            return;
        }

        $this->decoratePerex($file);
        $this->decorateContent($file);
    }

    private function decorateHeadlinesWithTocAnchors(string $htmlContent): string
    {
        return Strings::replace($htmlContent, '#<h([1-6])>(.*?)<\/h[1-6]>#', function (array $result): string {
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

    private function decoratePerex(AbstractFile $file): void
    {
        $configuration = $file->getConfiguration();
        if (! isset($configuration['perex'])) {
            return;
        }

        $markdownedPerexInParagraph = $this->parsedownExtra->text($configuration['perex']);

        // remove <p></p>
        $markdownedPerex = substr($markdownedPerexInParagraph, 3, -4);
        $configuration['perex'] = $markdownedPerex;

        $file->addConfiguration($configuration);
    }

    private function decorateContent(AbstractFile $file): void
    {
        $htmlContent = $this->parsedownExtra->text($file->getContent());

        if ($this->configuration->isMarkdownHeadlineAnchors()) {
            $htmlContent = $this->decorateHeadlinesWithTocAnchors($htmlContent);
        }

        $file->changeContent($htmlContent);
    }
}
