<?php declare(strict_types=1);

namespace Symplify\Statie\FlatWhite\Latte;

use Latte\Engine;
use Nette\Utils\Strings;

final class LatteRenderer
{
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
        $i = 0;
        $highlightedCodeBlocks = [];

        // due to StringLoader
        // make sure we have content and not file name
        $originalReference = $content;
        $content = $this->dynamicStringLoader->getContent($content);

        // replace code with placeholder
        $contentWithPlaceholders = Strings::replace(
            $content,
            self::CODE_BLOCKS_HTML_PATTERN,
            function (array $match) use (&$i, &$highlightedCodeBlocks) {
                $placeholder = self::PLACEHOLDER_PREFIX . ++$i;
                $highlightedCodeBlocks[$placeholder] = $match['code'];

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
            function (array $match) use ($highlightedCodeBlocks) {
                return $highlightedCodeBlocks[$match['placeholder']];
            }
        );
    }
}
