<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\Configuration;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\Psr4Switcher\Exception\ConfigurationException;
use Symplify\Psr4Switcher\ValueObject\Option;
use Symplify\SmartFileSystem\FileSystemGuard;

final class Psr4SwitcherConfiguration
{
    /**
     * @var string[]
     */
    private $source = [];

    /**
     * @var string
     */
    private $composerJsonPath;

    public function __construct(
        private FileSystemGuard $fileSystemGuard
    ) {
    }

    /**
     * For testing
     */
    public function loadForTest(string $composerJsonPath): void
    {
        $this->composerJsonPath = $composerJsonPath;
    }

    public function loadFromInput(InputInterface $input): void
    {
        $composerJsonPath = (string) $input->getOption(Option::COMPOSER_JSON);
        if ($composerJsonPath === '') {
            throw new ConfigurationException(sprintf('Provide composer.json via "--%s"', Option::COMPOSER_JSON));
        }

        $this->fileSystemGuard->ensureFileExists($composerJsonPath, __METHOD__);

        $this->composerJsonPath = $composerJsonPath;
        $this->source = (array) $input->getArgument(Option::SOURCES);
    }

    /**
     * @return string[]
     */
    public function getSource(): array
    {
        return $this->source;
    }

    public function getComposerJsonPath(): string
    {
        return $this->composerJsonPath;
    }
}
