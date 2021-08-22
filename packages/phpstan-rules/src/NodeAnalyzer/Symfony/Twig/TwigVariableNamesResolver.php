<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\Symfony\Twig;

use Symplify\PHPStanRules\Twig\NodeVisitor\VariableCollectingNodeVisitor;
use Symplify\SmartFileSystem\SmartFileSystem;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\NodeTraverser;
use Twig\Source;

final class TwigVariableNamesResolver
{
    public function __construct(
        private SmartFileSystem $smartFileSystem
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $filePath): array
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);

        $arrayLoader = new ArrayLoader([
            $filePath => $fileContent,
        ]);

        $environment = new Environment($arrayLoader);
        $tokenStream = $environment->tokenize(new Source($fileContent, $filePath));

        $moduleNode = $environment->parse($tokenStream);

        $variableCollectingNodeVisitor = new VariableCollectingNodeVisitor();
        $twigNodeTraverser = new NodeTraverser($environment, [$variableCollectingNodeVisitor]);

        $twigNodeTraverser->traverse($moduleNode);

        return $variableCollectingNodeVisitor->getVariableNames();
    }
}
