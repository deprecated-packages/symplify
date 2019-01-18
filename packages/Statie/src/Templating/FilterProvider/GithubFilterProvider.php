<?php declare(strict_types=1);

namespace Symplify\Statie\Templating\FilterProvider;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class GithubFilterProvider implements FilterProviderInterface
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
            // in Latte: <a href="{$post|githubEditPostUrl}">Typo? Fix me please</a>
            // in Twig: <a href="{{ post|githubEditPostUrl }}">Typo? Fix me please</a>
            'githubEditPostUrl' => function (AbstractFile $file): string {
                $editPrefix = $this->renameTreeToEdit($this->configuration->getGithubRepositorySourceDirectory());
                return $editPrefix . '/' . $file->getRelativeSource();
            },
        ];
    }

    private function renameTreeToEdit(string $string): string
    {
        return str_replace('/tree/', '/edit/', $string);
    }
}
