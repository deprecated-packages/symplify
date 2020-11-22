<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\TemplateChecker\Console\Output\MissingTwigTemplatePathReporter;
use Symplify\TemplateChecker\Template\RenderMethodTemplateExtractor;
use Symplify\TemplateChecker\Template\TemplatePathsResolver;
use Symplify\TemplateChecker\Twig\TwigAnalyzer;
use Symplify\TemplateChecker\ValueObject\Option;

final class CheckTwigRenderCommand extends AbstractSymplifyCommand
{
    /**
     * @var TemplatePathsResolver
     */
    private $templatePathsResolver;

    /**
     * @var RenderMethodTemplateExtractor
     */
    private $renderMethodTemplateExtractor;

    /**
     * @var TwigAnalyzer
     */
    private $twigAnalyzer;

    /**
     * @var MissingTwigTemplatePathReporter
     */
    private $missingTwigTemplatePathReporter;

    public function __construct(
        TemplatePathsResolver $possibleTemplatePathsResolver,
        RenderMethodTemplateExtractor $renderMethodTemplateExtractor,
        TwigAnalyzer $twigAnalyzer,
        MissingTwigTemplatePathReporter $missingTwigTemplatePathReporter
    ) {
        $this->templatePathsResolver = $possibleTemplatePathsResolver;
        $this->renderMethodTemplateExtractor = $renderMethodTemplateExtractor;

        parent::__construct();

        $this->twigAnalyzer = $twigAnalyzer;
        $this->missingTwigTemplatePathReporter = $missingTwigTemplatePathReporter;
    }

    protected function configure(): void
    {
        $this->setDescription('Validate template paths in $this->render(...)');
        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to project directories'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(Option::SOURCES);

        $this->symfonyStyle->title('Analysing controllers and templates');

        $stats = [];

        $controllerFileInfos = $this->smartFinder->find($sources, '#Controller\.php$#');
        $stats[] = sprintf('%d controllers', count($controllerFileInfos));

        $allowedTemplatePaths = $this->templatePathsResolver->resolveFromDirectories($sources);
        $stats[] = sprintf('%d twig paths', count($allowedTemplatePaths));

        $usedTemplatePaths = $this->renderMethodTemplateExtractor->extractFromFileInfos($controllerFileInfos);
        $stats[] = sprintf('%d unique used templates in "$this->render()" method', count($usedTemplatePaths));

        $this->symfonyStyle->listing($stats);
        $this->symfonyStyle->newLine(2);

        $errorMessages = $this->twigAnalyzer->analyzeFileInfos($controllerFileInfos, $allowedTemplatePaths);

        return $this->missingTwigTemplatePathReporter->report($errorMessages);
    }
}
