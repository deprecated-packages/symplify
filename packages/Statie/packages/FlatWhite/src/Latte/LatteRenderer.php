<?php declare(strict_types=1);

namespace Symplify\Statie\FlatWhite\Latte;

use Latte\Engine;
use Nette\Utils\Strings;

final class LatteRenderer
{
    /**
     * @var string
     * @see https://regex101.com/r/IgngFX/2
     */
    private const MATCH_CODE_BLOCKS = '#^```[a-z-]*$(?:(?!^```$).)+^```$#ms';

    /**
     * @var string
     */
    private const MATCH_PLACEHOLDERS = '#^' . self::PLACEHOLDER_PREFIX . '[0-9]+$#m';

    /**
     * @var string
     */
    private const PLACEHOLDER_PREFIX = '___replace_block_';

    /**
     * @var Engine
     */
    private $latte;

    public function __construct(LatteFactory $latteFactory)
    {
        $this->latte = $latteFactory->create();
    }

    /**
     * @param mixed[] $parameters
     */
    public function renderExcludingHighlightBlocks(string $content, array $parameters): string
    {
        $i = 0;
        $highlightedCodeBlocks = [];

        $contentWithPlaceholders = Strings::replace(
            $content,
            self::MATCH_CODE_BLOCKS,
            function (array $match) use (&$i, &$highlightedCodeBlocks) {
                $highlightedCodeBlock = $match[0];
                $placeholder = self::PLACEHOLDER_PREFIX . ++$i;
                $highlightedCodeBlocks[$placeholder] = $highlightedCodeBlock;

                return $placeholder;
            }
        );

        $renderedContentWithPlaceholders = $this->latte->renderToString($contentWithPlaceholders, $parameters);

        return Strings::replace(
            $renderedContentWithPlaceholders,
            self::MATCH_PLACEHOLDERS,
            function (array $match) use ($highlightedCodeBlocks) {
                $placeholder = $match[0];

                return $highlightedCodeBlocks[$placeholder];
            }
        );
    }
}
