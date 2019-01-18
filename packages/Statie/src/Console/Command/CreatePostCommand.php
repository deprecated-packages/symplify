<?php declare(strict_types=1);

namespace Symplify\Statie\Console\Command;

use Nette\Utils\FileSystem as NetteFileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\Exception\Configuration\ConfigurationException;
use Symplify\Statie\FileSystem\CreatePostFileSystem;
use Symplify\Statie\Generator\Configuration\GeneratorConfiguration;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use function Safe\sprintf;

final class CreatePostCommand extends Command
{
    /**
     * @var string
     */
    private const POST_KEY = 'post';

    /**
     * @var string
     */
    private $postTemplatePath;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var GeneratorConfiguration
     */
    private $generatorConfiguration;

    /**
     * @var CreatePostFileSystem
     */
    private $createPostFileSystem;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        GeneratorConfiguration $generatorConfiguration,
        string $postTemplatePath,
        CreatePostFileSystem $createPostFileSystem
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->generatorConfiguration = $generatorConfiguration;
        $this->postTemplatePath = $postTemplatePath;
        $this->createPostFileSystem = $createPostFileSystem;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Create a new post with id, date and title');
        $this->addArgument('title', InputArgument::REQUIRED, 'Title of new post');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $title = (string) $input->getArgument('title');

        $postGeneratorElement = $this->getPostGeneratorElementConfiguration();
        $this->ensurePostsPathExists($postGeneratorElement);

        $postFileContent = $this->generatePostFileContent($postGeneratorElement, $title);

        $postFilePath = $this->createPostAbsoluteFilePath($title, $postGeneratorElement);
        $this->savePostFileContent($postFilePath, $postFileContent);

        $this->symfonyStyle->writeln($postFileContent);
        $this->symfonyStyle->success(sprintf('Your new post is generated in "%s" with content above', $postFilePath));

        return ShellCode::SUCCESS;
    }

    private function getPostGeneratorElementConfiguration(): GeneratorElement
    {
        $generatorElements = $this->generatorConfiguration->getGeneratorElements();
        foreach ($generatorElements as $generatorElement) {
            if ($generatorElement->getVariable() === self::POST_KEY) {
                return $generatorElement;
            }
        }

        throw new ConfigurationException(sprintf('Generator element for "%s" was not found.', self::POST_KEY));
    }

    private function ensurePostsPathExists(GeneratorElement $postGeneratorElement): void
    {
        NetteFileSystem::createDir($postGeneratorElement->getPath());
    }

    private function generatePostFileContent(GeneratorElement $postGeneratorElement, string $title): string
    {
        $postFileContent = NetteFileSystem::read($this->postTemplatePath);

        return $this->applyVariables($postFileContent, [
            '__ID__' => $this->resolveNextPostIdFromPath($postGeneratorElement),
            '__TITLE__' => $title,
        ]);
    }

    private function createPostAbsoluteFilePath(string $title, GeneratorElement $generatorElement): string
    {
        return $generatorElement->getPath() . '/' . $this->createPostFileName($title, $generatorElement);
    }

    private function savePostFileContent(string $postFilePath, string $postFileContent): void
    {
        $this->createPostFileSystem->ensureFilePathIsNew($postFilePath);
        NetteFileSystem::write($postFilePath, $postFileContent);
    }

    /**
     * @param mixed[] $variables
     */
    private function applyVariables(string $content, array $variables): string
    {
        return str_replace(array_keys($variables), array_values($variables), $content);
    }

    private function resolveNextPostIdFromPath(GeneratorElement $generatorElement): int
    {
        $highestId = 0;

        foreach ($this->createPostFileSystem->findMarkdownFilesInGeneratorElement($generatorElement) as $postFileInfo) {
            $match = Strings::match($postFileInfo->getContents(), '#^id: (?<id>\d+)$#m');
            if ($match['id']) {
                $currentId = (int) $match['id'];
                $highestId = max($highestId, $currentId);
            }
        }

        return $highestId + 1;
    }

    /**
     * Returns format based on directory nesting or not:
     *
     * - 2019-01-01-some-post.md
     * - 2019/2019-01-01-some-post.md
     */
    private function createPostFileName(string $title, GeneratorElement $generatorElement): string
    {
        $webalizedDate = date('Y-m-d');
        $webalizedTitle = Strings::lower(Strings::webalize($title));

        $postFileName = sprintf('%s-%s.md', $webalizedDate, $webalizedTitle);

        if ($this->createPostFileSystem->isNestedByYear($generatorElement)) {
            $year = date('Y');
            $postFileName = $year . '/' . $postFileName;
        }

        return $postFileName;
    }
}
