<?php declare(strict_types=1);

namespace Symplify\Statie\Amp;

use Lullabot\AMP\AMP;
use Lullabot\AMP\Validate\Scope;

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
        $options = [
            'scope' => Scope::HTML_SCOPE,
            'canonical_path' => $originalUrl,
        ];
        $this->amp->loadHtml($html, $options);
        $this->amp->convertToAmpHtml();

        return $this->amp->getAmpHtml();
    }
}
