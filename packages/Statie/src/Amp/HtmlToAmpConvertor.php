<?php declare(strict_types=1);

namespace Symplify\Statie\Amp;

use Lullabot\AMP\AMP;
use Lullabot\AMP\Validate\Scope;
use Nette\Utils\Strings;
use Symplify\Statie\Exception\Amp\NonHtmlFileException;

final class HtmlToAmpConvertor
{
    /**
     * @var AMP
     */
    private $amp;

    public function __construct(AMP $amp)
    {
        $this->amp = $amp;
    }

    public function convert(string $html, string $originalUrl): string
    {
        $this->ensureContentIsHtml($originalUrl);

        $options = [
            'scope' => Scope::HTML_SCOPE,
            'canonical_path' => $originalUrl,
        ];

        $this->amp->loadHtml($html, $options);
        $this->amp->convertToAmpHtml();

        return $this->amp->getAmpHtml();
    }

    private function ensureContentIsHtml(string $url): void
    {
        if (! Strings::endsWith($url, '.html')) {
            throw new NonHtmlFileException(sprintf(
                'File "%s" is not html. AMP convertor only accepts html files.',
                $url
            ));
        }
    }
}
