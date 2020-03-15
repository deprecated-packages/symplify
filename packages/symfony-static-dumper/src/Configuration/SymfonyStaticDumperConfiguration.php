<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Configuration;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class SymfonyStaticDumperConfiguration
{
    /**
     * @var string
     */
    private $publicDirectory;

    /**
     * @var string
     */
    private $outputDirectory;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->publicDirectory = $parameterBag->get('kernel.project_dir') . '/public';
        $this->outputDirectory = getcwd() . '/output';
    }

    public function getPublicDirectory(): string
    {
        return $this->publicDirectory;
    }

    public function getOutputDirectory(): string
    {
        return $this->outputDirectory;
    }
}
