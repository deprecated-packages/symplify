<?php declare(strict_types=1);

namespace Symplify\Statie\Twig;

use Symplify\Statie\Renderable\CodeBlocksProtector;
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
     * @var CodeBlocksProtector
     */
    private $codeBlocksProtector;

    public function __construct(
        Environment $twigEnvironment,
        ArrayLoader $twigArrayLoader,
        CodeBlocksProtector $codeBlocksProtector
    ) {
        $this->twigEnvironment = $twigEnvironment;
        $this->twigArrayLoader = $twigArrayLoader;
        $this->codeBlocksProtector = $codeBlocksProtector;
    }

    /**
     * @param mixed[] $parameters
     */
    public function renderExcludingHighlightBlocks(AbstractFile $file, array $parameters): string
    {
        return $this->codeBlocksProtector->protectContentFromCallback($file->getContent(), function (string  $content) use (
    $file,
            $parameters
) {
            $this->twigArrayLoader->setTemplate($file->getFilePath(), $content);

            return $this->render($file, $parameters);
        });
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
}
