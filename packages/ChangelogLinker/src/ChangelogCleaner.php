<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Analyzer\LinksAnalyzer;

final class ChangelogCleaner
{
    /**
     * @var LinksAnalyzer
     */
    private $linksAnalyzer;

    public function __construct(LinksAnalyzer $linksAnalyzer)
    {
        $this->linksAnalyzer = $linksAnalyzer;
    }

    public function processContent(string $changelogContent): string
    {
        $this->linksAnalyzer->analyzeContent($changelogContent);

        $deadLinks = $this->linksAnalyzer->getDeadLinks();

        foreach ($deadLinks as $deadLink) {
            $changelogContent = Strings::replace($changelogContent, sprintf('#\[\#?(%s)\]:(.*?)\n#', $deadLink));
        }

        return $changelogContent;
    }
}
