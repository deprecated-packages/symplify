<?php declare(strict_types=1);

namespace Symplify\Statie\Twig;

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

    public function __construct(Environment $twigEnvironment, ArrayLoader $twigArrayLoader)
    {
        $this->twigEnvironment = $twigEnvironment;
        $this->twigArrayLoader = $twigArrayLoader;
    }

    /**
     * @param string[] $parameters
     */
    public function render(AbstractFile $file, array $parameters = []): string
    {
        $this->twigArrayLoader->setTemplate($file->getFilePath(), $file->getContent());

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
