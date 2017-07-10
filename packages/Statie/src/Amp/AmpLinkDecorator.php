<?php declare(strict_types=1);

namespace Symplify\Statie\Amp;

use Nette\Utils\Strings;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\DecoratorInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class AmpLinkDecorator implements DecoratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function decorateFile(AbstractFile $file): void
    {
        if (! Strings::endsWith($file->getOutputPath(), '.html')) {
            return;
        }

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
