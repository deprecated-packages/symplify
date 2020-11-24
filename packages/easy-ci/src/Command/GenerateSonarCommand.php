<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Sonar\SonarConfigGenerator;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\SmartFileInfo;

final class GenerateSonarCommand extends AbstractSymplifyCommand
{
    /**
     * @var string
     */
    private const SONAR_CONFIG_FILE_NAME = 'sonar-project.properties';

    /**
     * @var string[]
     */
    private $sonarDirectories = [];

    /**
     * @var SonarConfigGenerator
     */
    private $sonarConfigGenerator;

    /**
     * @var string
     */
    private $sonarConfigFilePath;

    /**
     * @var array<string, mixed|mixed[]>
     */
    private $sonarOtherParameters = [];

    public function __construct(ParameterProvider $parameterProvider, SonarConfigGenerator $sonarConfigGenerator)
    {
        $this->sonarDirectories = (array) $parameterProvider->provideParameter(Option::SONAR_DIRECTORIES);
        $this->sonarOtherParameters = (array) $parameterProvider->provideParameter(Option::SONAR_OTHER_PARAMETERS);
        $this->sonarConfigGenerator = $sonarConfigGenerator;

        $this->sonarConfigFilePath = getcwd() . '/' . self::SONAR_CONFIG_FILE_NAME;

        parent::__construct();
    }

    protected function configure(): void
    {
        $description = sprintf('Generate "%s" path', self::SONAR_CONFIG_FILE_NAME);
        $this->setDescription($description);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $generatedSonarFileContent = $this->sonarConfigGenerator->generate(
            $this->sonarDirectories,
            $this->sonarOtherParameters
        );
        $this->smartFileSystem->dumpFile($this->sonarConfigFilePath, $generatedSonarFileContent);

        $sonarConfigFileInfo = new SmartFileInfo($this->sonarConfigFilePath);
        $message = sprintf('File "%s" was generated', $sonarConfigFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
