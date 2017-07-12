<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\Latte;

use Latte\CompileException;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\DecoratorInterface;
use Symplify\Statie\Exception\Latte\InvalidLatteSyntaxException;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Symplify\Statie\FlatWhite\Latte\LatteRenderer;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\PostFile;

final class LatteDecorator implements DecoratorInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var DynamicStringLoader
     */
    private $dynamicStringLoader;

    /**
     * @var LatteRenderer
     */
    private $latteRenderer;

    public function __construct(
        Configuration $configuration,
        DynamicStringLoader $dynamicStringLoader,
        LatteRenderer $latteRenderer
    ) {
        $this->configuration = $configuration;
        $this->dynamicStringLoader = $dynamicStringLoader;
        $this->latteRenderer = $latteRenderer;
    }

    public function decorateFile(AbstractFile $file): void
    {
        $options = $this->configuration->getOptions();

        $parameters = $file->getConfiguration() + $options + [
            'posts' => $options['posts'] ?? [],
            'file' => $file,
        ];

        if ($file instanceof PostFile) {
            $parameters['post'] = $file;
        }

        $this->renderInnerPostContent($file, $parameters);

        $htmlContent = $this->renderOuterWithLayout($file, $parameters);
        $htmlContent = $this->trimLeftOverLayoutTag($file, $htmlContent);

        $file->changeContent($htmlContent);
    }

    private function addTemplateToDynamicLatteStringLoader(AbstractFile $file): void
    {
        $this->dynamicStringLoader->changeContent(
            $file->getBaseName(),
            $file->getContent()
        );
    }

    private function prependLayoutToFileContent(AbstractFile $file): void
    {
        if (! $file->getLayout()) {
            return;
        }

        $layoutLine = sprintf('{layout "%s"}', $file->getLayout());
        $file->changeContent($layoutLine . PHP_EOL . PHP_EOL . $file->getContent());
    }

    /**
     * @param mixed[] $parameters
     */
    private function renderInnerPostContent(AbstractFile $file, array $parameters): void
    {
        if ($file instanceof PostFile) {
            $this->addTemplateToDynamicLatteStringLoader($file);
            $htmlContent = $this->renderToString($file, $parameters);
            $file->changeContent($htmlContent);
        }
    }

    /**
     * @param mixed[] $parameters
     */
    private function renderOuterWithLayout(AbstractFile $file, array $parameters): string
    {
        $this->prependLayoutToFileContent($file);
        $this->addTemplateToDynamicLatteStringLoader($file);

        return $this->renderToString($file, $parameters);
    }

    private function trimLeftOverLayoutTag(AbstractFile $file, string $htmlContent): string
    {
        if ($file instanceof PostFile) {
            return preg_replace('/{layout "[a-z]+"}/', '', $htmlContent);
        }

        return $htmlContent;
    }

    /**
     * @param mixed[] $parameters
     */
    private function renderToString(AbstractFile $file, array $parameters): string
    {
        try {
            return $this->latteRenderer->renderExcludingHighlightBlocks($file->getBaseName(), $parameters);

        } catch (CompileException $latteCompileException) {
            throw new InvalidLatteSyntaxException(sprintf(
                'Invalid Latte syntax found in "%s" file: %s',
                $file->getFilePath(),
                $latteCompileException->getMessage()
            ));
        }
    }
}
