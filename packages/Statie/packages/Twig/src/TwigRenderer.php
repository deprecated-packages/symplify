<?php declare(strict_types=1);

namespace Symplify\Statie\Twig;

use Nette\Utils\Strings;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Twig\Exception\InvalidTwigSyntaxException;
use Throwable;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class TwigRenderer
{
    /**
     * @var Environment
     */
    private $twigEnvironment;

    /**
     * @var ArrayLoader
     */
    private $twigArrayLoader;

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

    public function __construct(Environment $twigEnvironment, ArrayLoader $twigArrayLoader)
    {
        $this->twigEnvironment = $twigEnvironment;
        $this->twigArrayLoader = $twigArrayLoader;
    }

    /**
     * @param mixed[] $parameters
     */
    public function renderExcludingHighlightBlocks(AbstractFile $file, array $parameters): string
    {
        $this->reset();

        // replace code with placeholder
        $contentWithPlaceholders = Strings::replace(
            $file->getContent(),
            self::CODE_BLOCKS_HTML_PATTERN,
            function (array $match): string {
                $placeholder = self::PLACEHOLDER_PREFIX . ++$this->codePlaceholderId;
                $this->highlightedCodeBlocks[$placeholder] = $match['code'];

                return $placeholder;
            }
        );

        // co-dependency of Latte\Engine
        $this->twigArrayLoader->setTemplate($file->getFilePath(), $contentWithPlaceholders);
        $renderedContentWithPlaceholders = $this->render($file, $parameters);

        // replace placeholder back with code
        return Strings::replace(
            $renderedContentWithPlaceholders,
            self::PLACEHOLDER_PATTERN,
            function (array $match): string {
                return $this->highlightedCodeBlocks[$match['placeholder']];
            }
        );
    }

    /**
     * @param string[] $parameters
     */
    private function render(AbstractFile $file, array $parameters = []): string
    {
        try {
            return $this->twigEnvironment->render($file->getFilePath(), $parameters);
        } catch (Throwable $throwable) {
            throw new InvalidTwigSyntaxException(sprintf(
                'Invalid Twig syntax found or missing value in "%s" file: %s',
                $file->getFilePath(),
                $throwable->getMessage()
            ));
        }
    }

    private function reset(): void
    {
        $this->codePlaceholderId = 0;
        $this->highlightedCodeBlocks = [];
    }
}
