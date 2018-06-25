<?php declare(strict_types=1);

namespace Symplify\Statie\Twig;

use Latte\Runtime\FilterExecutor;
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

    /**
     * @var FilterExecutor
     */
    private $filterExecutor;

    public function __construct(ArrayLoader $arrayLoader, string $twigCacheDirectory, FilterExecutor $filterExecutor)
    {
        $this->arrayLoader = $arrayLoader;
        $this->twigCacheDirectory = $twigCacheDirectory;
        $this->filterExecutor = $filterExecutor;
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

        $this->loadLatteFilters($twigEnvironment);

        foreach ($this->filterProviders as $filterProvider) {
            foreach ($filterProvider->provide() as $name => $filter) {
                $twigEnvironment->addFilter(new Twig_Filter($name, $filter));
            }
        }

        return $twigEnvironment;
    }

    /**
     * @see https://github.com/nette/latte/blob/edcda892aee632c810697d9795c4fb065cd51506/src/Latte/Runtime/FilterExecutor.php
     */
    private function loadLatteFilters(Environment $twigEnvironment): void
    {
        foreach ($this->filterExecutor->getAll() as $name => $filter) {
            $twigEnvironment->addFilter(new Twig_Filter($name, $this->filterExecutor->{$filter}));
        }
    }
}
