<?php declare(strict_types=1);

namespace Symplify\Statie\Application\Command;

final class RunCommand
{
    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var string
     */
    private $outputDirectory;

    public function __construct(string $sourceDirectory, string $outputDirectory)
    {
        $this->sourceDirectory = $sourceDirectory;
        $this->outputDirectory = $outputDirectory;
    }

    public function getOutputDirectory() : string
    {
        return $this->outputDirectory;
    }

    public function getSourceDirectory() : string
    {
        return $this->sourceDirectory;
    }
}
