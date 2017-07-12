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
    public function renderExcludingHighlightBlocks(string $templateFileContent, array $parameters): string
    {
        $i = 0;
        $blocksWithHighlightedContents = [];

        $templateWithPlaceholders = Strings::replace(
            $templateFileContent,
            self::MATCH_CODE_BLOCKS,
            function (array $match) use (&$i, &$blocksWithHighlightedContents) {
                $highlightedContents = $match[0];
                $placeholder = self::PLACEHOLDER_PREFIX . ++$i;
                $blocksWithHighlightedContents[$placeholder] = $highlightedContents;

                return $placeholder;
            }
        );

        $parseTemplateWithPlaceholders = $this->latte->renderToString($templateWithPlaceholders, $parameters);

        return Strings::replace(
            $parseTemplateWithPlaceholders,
            self::MATCH_PLACEHOLDERS,
            function (array $match) use ($blocksWithHighlightedContents) {
                $placeholder = $match[0];

                return $blocksWithHighlightedContents[$placeholder];
            }
        );
    }
}
