<?php declare(strict_types=1);

namespace Symplify\Statie\Twig;

use Iterator;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Renderable\File\AbstractFile;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class GithubTwigExtension extends AbstractExtension
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
     * @return TwigFilter[]
     */
    public function getFilters(): Iterator
    {
        // in Twig: <a href="{{ post|github_edit_post_url }}">Typo? Fix me please</a>
        yield new TwigFilter('github_edit_post_url', function (AbstractFile $file): string {
            return $this->createGithubEditFileUrl($file);
        });
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
