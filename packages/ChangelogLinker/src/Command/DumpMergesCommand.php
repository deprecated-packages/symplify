<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ChangelogLinker\Regex\RegexPattern;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

/**
 * @inspired by https://github.com/weierophinney/changelog_generator
 */
final class DumpMergesCommand extends Command
{
    /**
     * @var string
     */
    private $repositoryName;

    /**
     * @var string
     */
    private $maintainer;

    /**
     * @var Client
     */
    private $client;

    public function __construct(string $repositoryName, string $maintainer, Client $client)
    {
        $this->repositoryName = $repositoryName;
        $this->maintainer = $maintainer;
        $this->client = $client;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription(
            'Scans repository merged PRs, that are not in the CHANGELOG.md yet, and dumps them in changelog format.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lastIdInChangelog = $this->getLastIdInChangelog();

        $url = sprintf('https://api.github.com/repos/%s/pulls?state=all', $this->repositoryName);
        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            // error
            return 1;
        }

        $result = $this->createJsonArrayFromResponse($response);

        foreach ($result as $pullRequest) {
            if ($pullRequest['number'] < $lastIdInChangelog) {
                continue;
            }

            $pullRequestMessage = sprintf('- [#%s] %s', $pullRequest['number'], $pullRequest['title']);

            $pullRequestAuthor = $pullRequest['user']['login'];

            // skip the main maintainer to prevent self-thanking floods
            if ($pullRequestAuthor !== $this->maintainer) {
                $pullRequestMessage .= ', Thanks to @' . $pullRequestAuthor;
            }

            $output->writeln($pullRequestMessage);
        }

        // success
        return 0;
    }

    private function getLastIdInChangelog(): int
    {
        $changelogContent = file_get_contents(getcwd() . '/CHANGELOG.md');

        $match = Strings::match($changelogContent, '#' . RegexPattern::PR_OR_ISSUE . '#');
        if ($match) {
            return (int) $match['id'];
        }

        return 1;
    }

    /**
     * @return mixed[]
     */
    private function createJsonArrayFromResponse(Response $response): array
    {
        return Json::decode((string) $response->getBody(), Json::FORCE_ARRAY);
    }
}
