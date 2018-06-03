<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Command;

use GuzzleHttp\Client;
use Nette\Utils\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
    private $author;

    public function __construct(string $repositoryName, string $author)
    {
        $this->repositoryName = $repositoryName;
        $this->author = $author;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument('pull-id', InputArgument::REQUIRED, 'Since pull request ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // @todo use the one on CHANGELOG as default
        $sincePullRequestId = (int) $input->getArgument('pull-id');

        $client = new Client();

        $url = sprintf( 'https://api.github.com/repos/%s/pulls?state=all', $this->repositoryName);
        $response = $client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            // error
            return 1;
        }

        $result = Json::decode((string) $response->getBody(), Json::FORCE_ARRAY);
        foreach($result as $pullRequest) {
            if ($pullRequest['number'] < $sincePullRequestId) {
                continue;
            }

            $pullRequestMessage = sprintf('- [#%s] %s', $pullRequest['number'], $pullRequest['title']);

            $pullRequestAuthor = $pullRequest['user']['login'];
            if ($pullRequestAuthor !== $this->author) {
                $pullRequestMessage .= ', Thanks to @' . $pullRequestAuthor;
            }

            $output->writeln($pullRequestMessage);
        }

        // success
        return 0;
    }
}
