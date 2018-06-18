<?php declare(strict_types=1);

namespace Symplify\Statie\Latte;

use Latte\Engine;
use Symplify\Statie\Latte\Loader\ArrayLoader;
use Symplify\Statie\Renderable\CodeBlocksProtector;
use Symplify\Statie\Renderable\File\AbstractFile;

final class LatteRenderer
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
    public function render(AbstractFile $file, array $parameters): string
    {
        return $this->codeBlocksProtector->protectContentFromCallback($file->getContent(), function (string  $content) use (
    $file,
            $parameters
) {
            $this->arrayLoader->changeContent($file->getFilePath(), $content);

            return $this->engine->renderToString($file->getFilePath(), $parameters);
        });
    }
}
