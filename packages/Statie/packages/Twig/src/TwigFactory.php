<?php declare(strict_types=1);

namespace Symplify\Statie\Twig;

use Nette\Utils\Strings;
use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\Contract\Templating\FilterProvidersAwareInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\LoaderInterface;
use Twig_Filter;

final class TwigFactory implements FilterProvidersAwareInterface
{
    /**
     * @var LoaderInterface
     */
    private $arrayLoader;

    /**
     * @var string
     */
    private $twigCacheDirectory;

    /**
     * @var FilterProviderInterface[]
     */
    private $filterProviders = [];

    public function __construct(ArrayLoader $arrayLoader, string $twigCacheDirectory)
    {
        $this->arrayLoader = $arrayLoader;
        $this->twigCacheDirectory = $twigCacheDirectory;
    }

    public function addFilterProvider(FilterProviderInterface $filterProvider): void
    {
        $this->filterProviders[] = $filterProvider;
    }

    public function create(): Environment
    {
        $twigEnvironment = new Environment($this->arrayLoader, [
            'cache' => $this->twigCacheDirectory,
        ]);

        // add "webalize"
        $twigEnvironment->addFilter(
            new Twig_Filter('webalize', function (string $content, ?string $chalist, ?bool $lower = true) {
                return Strings::webalize($content, $chalist, $lower);
            })
        );

        foreach ($this->filterProviders as $filterProvider) {
            foreach ($filterProvider->provide() as $name => $filter) {
                $twigEnvironment->addFilter(new Twig_Filter($name, $filter));
            }
        }

        return $twigEnvironment;
    }
}
