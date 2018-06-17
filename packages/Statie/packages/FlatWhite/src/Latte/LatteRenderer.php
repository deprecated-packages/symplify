<?php declare(strict_types=1);

namespace Symplify\Statie\FlatWhite\Latte;

use Latte\Engine;
use Nette\Utils\Strings;
use Symplify\Statie\Renderable\File\AbstractFile;

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
     * @var int
     */
    private $lattePlaceholderId = 0;

    /**
     * @var Engine
     */
    private $engine;

    /**
     * @var ArrayLoader
     */
    private $arrayLoader;

    /**
     * @var string[]
     */
    private $highlightedCodeBlocks = [];

    public function __construct(LatteFactory $latteFactory, ArrayLoader $arrayLoader)
    {
        $this->engine = $latteFactory->create();
        $this->arrayLoader = $arrayLoader;
    }

    /**
     * @param mixed[] $parameters
     */
    public function renderExcludingHighlightBlocks(AbstractFile $file, array $parameters): string
    {
        $this->arrayLoader->changeContent($file->getFilePath(), $file->getContent());

        $this->lattePlaceholderId = 0;
        $this->highlightedCodeBlocks = [];

        // replace code with placeholder
        $contentWithPlaceholders = Strings::replace(
            $file->getContent(),
            self::CODE_BLOCKS_HTML_PATTERN,
            function (array $match): string {
                $placeholder = self::PLACEHOLDER_PREFIX . ++$this->lattePlaceholderId;
                $this->highlightedCodeBlocks[$placeholder] = $match['code'];

                return $placeholder;
            }
        );

        // due to StringLoader
        $this->arrayLoader->changeContent($file->getFilePath(), $contentWithPlaceholders);
        $renderedContentWithPlaceholders = $this->engine->renderToString($file->getFilePath(), $parameters);

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
