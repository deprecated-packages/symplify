<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\SingleFileProcessor;
use Symplify\EasyCodingStandard\Configuration\Exception\NoMarkdownFileException;
use Symplify\SmartFileSystem\SmartFileInfo;

final class MarkdownCodeFormatterCommand extends Command
{
    /**
     * @var SingleFileProcessor
     */
    private $singleFileProcessor;

    public function __construct(SingleFileProcessor $singleFileProcessor)
    {
        $this->singleFileProcessor = $singleFileProcessor;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('markdown-code-format');
        $this->setDescription('Format markdown code');
        $this->addArgument('markdown-file', InputArgument::REQUIRED, 'The markdown file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $markdownFile = $input->getArgument('markdown-file');
        if (! file_exists($markdownFile)) {
            throw new NoMarkdownFileException(sprintf('Markdown file %s not found', $markdownFile));
        }

        $content = file_get_contents($markdownFile);
        preg_match_all('#\`\`\`php\s+([^\`\`\`]+)\s+\`\`\`#', $content, $matches);

        if (empty($matches[1])) {
            return 0;
        }

        foreach ($matches[1] as $key => $match) {
            $file = "php-code-${key}.php";
            file_put_contents($file, trim($match));

            $fileInfo = new SmartFileInfo($file);
            $this->singleFileProcessor->processFileInfo($fileInfo);
        }

        return 0;
    }
}
