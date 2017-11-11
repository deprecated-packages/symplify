<?php declare(strict_types=1);

namespace Symplify\Statie\FlatWhite\Latte;

use Latte\Engine;
use Latte\ILoader;
use Symplify\Statie\Contract\Templating\FilterProviderInterface;

final class LatteFactory
{
    /**
     * @var ILoader
     */
    private $loader;

    /**
     * @var FilterProviderInterface[]
     */
    private $filterProviders = [];

    public function __construct(ILoader $loader)
    {
        $this->loader = $loader;
    }

    public function addFilterProvider(FilterProviderInterface $filterProvider): void
    {
        $this->filterProviders[] = $filterProvider;
    }

    public function create(): Engine
    {
        $latteEngine = new Engine();
        $latteEngine->setLoader($this->loader);
        $latteEngine->setTempDirectory(sys_get_temp_dir() . '/_flat_white_latte_factory_cache');

        foreach ($this->filterProviders as $filterProvider) {
            foreach ($filterProvider->provide() as $name => $filter) {
                $latteEngine->addFilter($name, $filter);
            }
        }

        return $latteEngine;
    }
}
