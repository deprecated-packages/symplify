<?php declare(strict_types=1);

namespace Symplify\Statie\Twig;

use Latte\Runtime\FilterExecutor;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Loader\ArrayLoader;
use Twig\Loader\LoaderInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class TwigFactory
{
    /**
     * @var string
     */
    private $twigCacheDirectory;

    /**
     * @var AbstractExtension[]
     */
    private $extensions = [];

    /**
     * @var LoaderInterface
     */
    private $arrayLoader;

    /**
     * @var FilterExecutor
     */
    private $filterExecutor;

    /**
     * @param AbstractExtension[] $extensions
     */
    public function __construct(
        ArrayLoader $arrayLoader,
        string $twigCacheDirectory,
        FilterExecutor $filterExecutor,
        array $extensions
    ) {
        $this->arrayLoader = $arrayLoader;
        $this->twigCacheDirectory = $twigCacheDirectory;
        $this->filterExecutor = $filterExecutor;
        $this->extensions = $extensions;
    }

    public function create(): Environment
    {
        $environment = new Environment($this->arrayLoader, [
            'cache' => $this->twigCacheDirectory,
        ]);

        // report missing variables, it's easier to debug code then in case of typo
        $environment->enableStrictVariables();

        $this->loadLatteFilters($environment);

        foreach ($this->extensions as $extension) {
            $environment->addExtension($extension);
        }

        return $environment;
    }

    /**
     * @see https://github.com/nette/latte/blob/edcda892aee632c810697d9795c4fb065cd51506/src/Latte/Runtime/FilterExecutor.php
     */
    private function loadLatteFilters(Environment $environment): void
    {
        foreach ($this->filterExecutor->getAll() as $name => $filter) {
            $environment->addFilter(new TwigFilter($name, $this->filterExecutor->{$filter}));
            $environment->addFunction(new TwigFunction($name, $this->filterExecutor->{$filter}));
        }
    }
}
