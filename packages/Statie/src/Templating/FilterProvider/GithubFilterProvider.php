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
            // in Twig (common format): <a href="{{ post|github_edit_post_url }}">Typo? Fix me please</a>
            'github_edit_post_url' => function (AbstractFile $file): string {
                return $this->createGithubEditFileUrl($file);
            },
        ];
    }

    private function createGithubEditFileUrl(AbstractFile $file): string
    {
        $editPrefix = $this->renameTreeToEdit($this->statieConfiguration->getGithubRepositorySourceDirectory());

        return $editPrefix . '/' . $file->getRelativeSource();
    }

    private function renameTreeToEdit(string $string): string
    {
        return str_replace('/tree/', '/edit/', $string);
    }
}
