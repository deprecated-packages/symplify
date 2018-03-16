<?php declare(strict_types=1);

namespace Symplify\Statie\FlatWhite\Latte;

use Latte\Engine;
use Nette\Utils\Strings;

final class LatteRenderer
{
    /**
     * @var int
     */
    private $lattePlaceholderId = 0;

    /**
     * @var string
     */
    private const CODE_BLOCKS_HTML_PATTERN = '#(?<code><code(?: class=\"[a-z-]+\")?>*(?:(?!<\/code>).)+<\/code>)#ms';

    /**
     * @var string
     */
    private const PLACEHOLDER_PATTERN = '#(?<placeholder>' . self::PLACEHOLDER_PREFIX . '[0-9]+)#m';

    /**
     * @var string
     */
    private const PLACEHOLDER_PREFIX = '___replace_block___';

    /**
     * @var Engine
     */
    private $engine;

    /**
     * @var DynamicStringLoader
     */
    private $dynamicStringLoader;

    /**
     * @var string[]
     */
    private $highlightedCodeBlocks = [];

    public function __construct(LatteFactory $latteFactory, DynamicStringLoader $dynamicStringLoader)
    {
        $this->engine = $latteFactory->create();
        $this->dynamicStringLoader = $dynamicStringLoader;
    }

    /**
     * @param mixed[] $parameters
     */
    public function renderExcludingHighlightBlocks(string $content, array $parameters): string
    {
        $this->lattePlaceholderId = 0;
        $this->highlightedCodeBlocks = [];

        // due to StringLoader
        // make sure we have content and not file name
        $originalReference = $content;
        $content = $this->dynamicStringLoader->getContent($content);

        // replace code with placeholder
        $contentWithPlaceholders = Strings::replace(
            $content,
            self::CODE_BLOCKS_HTML_PATTERN,
            function (array $match): string {
                $placeholder = self::PLACEHOLDER_PREFIX . ++$this->lattePlaceholderId;
                $this->highlightedCodeBlocks[$placeholder] = $match['code'];

                return $placeholder;
            }
        );

        // due to StringLoader
        $this->dynamicStringLoader->changeContent($originalReference, $contentWithPlaceholders);
        $renderedContentWithPlaceholders = $this->engine->renderToString($originalReference, $parameters);

        // replace placeholder back with code
        return Strings::replace(
            $renderedContentWithPlaceholders,
            self::PLACEHOLDER_PATTERN,
            function (array $match): string {
                return $this->highlightedCodeBlocks[$match['placeholder']];
            }
        );
    }
}
