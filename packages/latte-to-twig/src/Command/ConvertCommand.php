<?php

declare(strict_types=1);

namespace Symplify\LatteToTwig\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\LatteToTwig\LatteToTwigConverter;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\ValueObject\Option;

final class ConvertCommand extends AbstractSymplifyCommand
{
    /**
     * @see https://regex101.com/r/Q5NJ4c/1
     * @var string
     */
    private const LATTE_SUFFIX_REGEX = '#\.latte$#';

    /**
     * @var LatteToTwigConverter
     */
    private $latteToTwigConverter;

    public function __construct(LatteToTwigConverter $latteToTwigConverter)
    {
        $this->latteToTwigConverter = $latteToTwigConverter;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Directories or files to convert'
        );
        $this->setDescription('Converts Latte syntax to Twig');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $fileInfos = $this->smartFinder->find($sources, '#\.(twig|latte)$#');

        foreach ($fileInfos as $fileInfo) {
            $convertedContent = $this->latteToTwigConverter->convertFile($fileInfo);
            $oldFilePath = $fileInfo->getPathname();
            $newFilePath = Strings::replace($fileInfo->getPathname(), self::LATTE_SUFFIX_REGEX, '.twig');

            // save
            $this->smartFileSystem->dumpFile($newFilePath, $convertedContent);

            // remove old path
            if ($oldFilePath !== $newFilePath) {
                $this->smartFileSystem->remove($oldFilePath);
            }
            $message = sprintf('File "%s" was converted to Twig to "%s"', $oldFilePath, $newFilePath);

            $this->symfonyStyle->note($message);
        }

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }
}
