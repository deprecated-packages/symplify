<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\Markdown;

use Nette\Utils\Strings;
use ParsedownExtra;
use Spatie\Regex\MatchResult;
use Spatie\Regex\Regex;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\DecoratorInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class MarkdownDecorator implements DecoratorInterface
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

    public function decorateFile(AbstractFile $file) : void
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

    private function decorateHeadlinesWithTocAnchors(string $htmlContent) : string
    {
        return Regex::replace('/<h([1-6])>(.*?)<\/h([1-6])>/', function (MatchResult $result) {
            $headline = $result->group(2);
            $headlineId = Strings::webalize($result->group(2));
            $iconLink = '<a class="anchor" href="#' . $headlineId . '" '.
                'aria-hidden="true"><span class="anchor-icon">#</span></a>';

            return sprintf(
                '<h%s id="%s">' .
                $iconLink . '%s</h%s>',
                $result->group(1),
                $headlineId,
                $headline,
                $result->group(1)
            );
        }, $htmlContent)->result();
    }
}
