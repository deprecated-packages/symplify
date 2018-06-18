<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Nette\Utils\Strings;

/**
 * This services protects code snippets in content by placeholders,
 * to prevent it from rendering. E.g. there it Latte/Twig code snippet in the post, but it should not be rendered
 * to HTML during rendering, but kept.
 */
final class CodeBlocksProtector
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
     * @var int
     */
    private $codePlaceholderId = 0;

    /**
     * @var string[]
     */
    private $highlightedCodeBlocks = [];

    public function protectContentFromCallback(string $content, callable $callable): string
    {
        $this->reset();

        $contentWithPlaceholders = $this->replaceCodeBlocksByPlaceholders($content);

        $processedContentWithPlaceholders = $callable($contentWithPlaceholders);

        return $this->replacePlaceholdersByCodeBlocks($processedContentWithPlaceholders);
    }

    private function reset(): void
    {
        $this->codePlaceholderId = 0;
        $this->highlightedCodeBlocks = [];
    }

    private function replaceCodeBlocksByPlaceholders(string $content): string
    {
        return Strings::replace(
            $content,
            self::CODE_BLOCKS_HTML_PATTERN,
            function (array $match): string {
                $placeholder = self::PLACEHOLDER_PREFIX . ++$this->codePlaceholderId;
                $this->highlightedCodeBlocks[$placeholder] = $match['code'];

                return $placeholder;
            }
        );
    }

    private function replacePlaceholdersByCodeBlocks(string $content): string
    {
        return Strings::replace(
            $content,
            self::PLACEHOLDER_PATTERN,
            function (array $match): string {
                return $this->highlightedCodeBlocks[$match['placeholder']];
            }
        );
    }
}
