<?php declare(strict_types=1);

namespace Symplify\Statie\Latte;

use Latte\CompileException;
use Latte\Engine;
use Symplify\Statie\Contract\Templating\RendererInterface;
use Symplify\Statie\Exception\Renderable\File\AccessKeyNotAvailableException;
use Symplify\Statie\Latte\Exception\InvalidLatteSyntaxException;
use Symplify\Statie\Latte\Loader\ArrayLoader;
use Symplify\Statie\Renderable\CodeBlocksProtector;
use Symplify\Statie\Renderable\File\AbstractFile;

final class LatteRenderer implements RendererInterface
{
    /**
     * @var Engine
     */
    private $engine;

    /**
     * @var ArrayLoader
     */
    private $arrayLoader;

    /**
     * @var CodeBlocksProtector
     */
    private $codeBlocksProtector;

    public function __construct(
        LatteFactory $latteFactory,
        ArrayLoader $arrayLoader,
        CodeBlocksProtector $codeBlocksProtector
    ) {
        $this->engine = $latteFactory->create();
        $this->arrayLoader = $arrayLoader;
        $this->codeBlocksProtector = $codeBlocksProtector;
    }

    /**
     * @param mixed[] $parameters
     */
    public function renderFileWithParameters(AbstractFile $file, array $parameters): string
    {
        $renderCallback = function (string $content) use ($file, $parameters) {
            $this->arrayLoader->changeContent($file->getFilePath(), $content);

            return $this->engine->renderToString($file->getFilePath(), $parameters);
        };

        try {
            return $this->codeBlocksProtector->protectContentFromCallback($file->getContent(), $renderCallback);
        } catch (CompileException | AccessKeyNotAvailableException $exception) {
            throw new InvalidLatteSyntaxException(sprintf(
                'Invalid Latte syntax found or missing value in "%s" file: %s',
                $file->getFilePath(),
                $exception->getMessage()
            ));
        }
    }
}
