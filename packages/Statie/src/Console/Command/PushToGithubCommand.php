<?php declare(strict_types=1);

namespace Symplify\Statie\Console\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\Statie\Github\GihubPublishingProcess;

final class PushToGithubCommand extends Command
{
    /**
     * @var GihubPublishingProcess
     */
    private $gihubPublishingProcess;

    public function __construct(GihubPublishingProcess $gihubPublishingProcess)
    {
        $this->gihubPublishingProcess = $gihubPublishingProcess;

        parent::__construct();
    }

    protected function configure() : void
    {
        $this->setName('push-to-github');
        $this->setDescription('Push generated site to Github pages.');
        $this->addArgument(
            'repository-slug',
            InputArgument::REQUIRED,
            'Repository slug, e.g. "TomasVotruba/tomasvotruba.cz".'
        );
        $this->addOption('token', null, InputOption::VALUE_REQUIRED, 'Github token.');
        $this->addOption(
            'output',
            null,
            InputOption::VALUE_REQUIRED,
            'Directory where was output saved TO.',
            getcwd() . DIRECTORY_SEPARATOR . 'output'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->ensureInputIsValid($input);

        $githubRepository = $this->createGithubRepositoryUrlWithToken(
            $input->getOption('token'),
            $input->getArgument('repository-slug')
        );

        $this->gihubPublishingProcess->pushDirectoryContentToRepository(
            $input->getOption('output'),
            $githubRepository
        );

        $output->writeln('<info>Website was successfully pushed to Github pages.</info>');

        return 0;
    }

    private function ensureInputIsValid(InputInterface $input) : void
    {
        $this->ensureTokenOptionIsSet((string) $input->getOption('token'));
        $this->ensureGithubRepositorySlugIsValid($input->getArgument('repository-slug'));
    }

    private function ensureTokenOptionIsSet(string $token) : void
    {
        if ($token === '') {
            throw new Exception('Set token value via "--token=<GITHUB_TOKEN>" option.');
        }
    }

    private function ensureGithubRepositorySlugIsValid(string $repositorySlug) : void
    {
        $repositoryUrl = 'https://github.com/' . $repositorySlug;
        if (! $this->doesUrlExist($repositoryUrl)) {
            throw new Exception(sprintf(
                'Repository "%s" is not accessible. Try fixing the "%s" slug.',
                $repositoryUrl,
                $repositorySlug
            ));
        }
    }

    private function doesUrlExist(string $url) : bool
    {
        $fileHeaders = @get_headers($url);
        if (! $fileHeaders || $fileHeaders[0] === 'HTTP/1.1 404 Not Found') {
            return false;
        }

        return true;
    }

    private function createGithubRepositoryUrlWithToken(string $token, string $repositorySlug) : string
    {
        return sprintf(
            'https://%s@github.com/%s.git',
            $token,
            $repositorySlug
        );
    }
}
