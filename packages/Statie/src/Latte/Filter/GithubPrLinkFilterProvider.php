<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Filter;

use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\AbstractFile;

final class GithubPrLinkFilterProvider implements LatteFiltersProviderInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            'githubEditPostUrl' => function (AbstractFile $file) {
                return 'https://github.com/'
                    . $this->configuration->getGithubRepositorySlug()
                    . '/edit/master/source/'
                    . $file->getRelativeSource();
            },
        ];
    }
}
