<?php declare(strict_types=1);

namespace Symplify\Statie\Amp;

use Lullabot\AMP\AMP;
use Lullabot\AMP\Validate\Scope;
use Nette\Caching\Cache;
use Nette\Utils\Strings;
use Symplify\Statie\Cache\CacheFactory;
use Symplify\Statie\Exception\Amp\NonHtmlFileException;

final class HtmlToAmpConvertor
{
    /**
     * @var AMP
     */
    private $amp;

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(AMP $amp, CacheFactory $cacheFactory)
    {
        $this->amp = $amp;
        $this->cache = $cacheFactory->create();
    }

    public function convert(string $html, string $originalUrl): string
    {
        $this->ensureContentIsHtml($originalUrl);

        $key = md5($html);

        $convertedFileContent = $this->cache->load($key);
        if ($convertedFileContent) {
            return $convertedFileContent;
        }

        $options = [
            'scope' => Scope::HTML_SCOPE,
            'canonical_path' => $originalUrl,
        ];

        $this->amp->loadHtml($html, $options);
        $this->amp->convertToAmpHtml();

        $ampHtml = $this->amp->getAmpHtml();
        $this->cache->save($key, $ampHtml);

        return $ampHtml;
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
