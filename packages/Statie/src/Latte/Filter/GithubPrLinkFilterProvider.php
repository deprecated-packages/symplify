<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Filter;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class GithubPrLinkFilterProvider implements FilterProviderInterface
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
    public function provide(): array
    {
        return [
            // @todo usage
            'githubEditPostUrl' => function (AbstractFile $file) {
                return 'https://github.com/'
                    . $this->configuration->getGithubRepositorySlug()
                    . '/edit/master/source/'
                    . $file->getRelativeSource();
            },
        ];
    }
}
