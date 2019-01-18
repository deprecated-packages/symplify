<?php declare(strict_types=1);

namespace Symplify\Statie\Templating\FilterProvider;

use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class GithubFilterProvider implements FilterProviderInterface
{
    /**
     * @var StatieConfiguration
     */
    private $statieConfiguration;

    public function __construct(StatieConfiguration $statieConfiguration)
    {
        $this->statieConfiguration = $statieConfiguration;
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
                $editPrefix = $this->renameTreeToEdit($this->statieConfiguration->getGithubRepositorySourceDirectory());
                return $editPrefix . '/' . $file->getRelativeSource();
            },
        ];
    }

    private function renameTreeToEdit(string $string): string
    {
        return str_replace('/tree/', '/edit/', $string);
    }
}
