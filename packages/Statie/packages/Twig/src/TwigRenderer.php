<?php

declare(strict_types=1);

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
     * @param string[] $parameters
     */
    public function renderFileWithParameters(AbstractFile $file, array $parameters): string
    {
        $renderCallback = function (string $content) use ($file, $parameters): string {
            $this->twigArrayLoader->setTemplate($file->getFilePath(), $content);

            return $this->twigEnvironment->render($file->getFilePath(), $parameters);
        };

        try {
            return $this->codeBlocksProtector->protectContentFromCallback($file->getContent(), $renderCallback);
        } catch (Throwable $throwable) {
            throw new InvalidTwigSyntaxException(sprintf(
                'Invalid Twig syntax found or missing value in "%s" file: %s',
                $file->getFilePath(),
                $throwable->getMessage()
            ), $throwable->getCode(), $throwable);
        }
    }
}
