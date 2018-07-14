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
    private const CODE_BLOCKS_HTML_PATTERN = '#(?<code><code(?: class=\"[\w-]+\")?>(.*?)<\/code>)#ms';

    /**
     * @var string
     */
    private const MARKDOWN_CODE_BLOCKPATTERN = '#(?<code>```(\w*)(.*?)```)#ms';

    /**
     * @var string
     */
    private const PLACEHOLDER_PATTERN = '#(?<placeholder>' . self::PLACEHOLDER_PREFIX . '\d+)#';

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

    public function replaceCodeBlocksByPlaceholders(string $content): string
    {
        $content = $this->replaceHtmlCodeBlocksByPlaceholders($content);

        return $this->replaceMarkdownCodeBlocksByPlaceholders($content);
    }

    private function replaceMarkdownCodeBlocksByPlaceholders(string $content): string
    {
        return Strings::replace(
            $content,
            self::MARKDOWN_CODE_BLOCKPATTERN,
            function (array $match): string {
                $placeholder = self::PLACEHOLDER_PREFIX . ++$this->codePlaceholderId;
                $this->highlightedCodeBlocks[$placeholder] = $match['code'];

                return $placeholder;
            }
        );
    }

    private function reset(): void
    {
        $this->codePlaceholderId = 0;
        $this->highlightedCodeBlocks = [];
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

    private function replaceHtmlCodeBlocksByPlaceholders(string $content): string
    {
        return $content = Strings::replace(
            $content,
            self::CODE_BLOCKS_HTML_PATTERN,
            function (array $match): string {
                $placeholder = self::PLACEHOLDER_PREFIX . ++$this->codePlaceholderId;
                $this->highlightedCodeBlocks[$placeholder] = $match['code'];

                return $placeholder;
            }
        );
    }
}
