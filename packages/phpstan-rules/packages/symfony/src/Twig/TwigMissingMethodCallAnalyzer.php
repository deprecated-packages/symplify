<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Twig;

use PhpParser\Node\Expr\Array_;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Symfony\ObjectTypeMethodAnalyzer;
use Symplify\PHPStanRules\Symfony\Twig\TwigNodeTravser\TwigNodeTraverserFactory;
use Symplify\PHPStanRules\Symfony\Twig\TwigNodeVisitor\MissingMethodCallNodeVisitor;
use Symplify\PHPStanRules\Symfony\TypeAnalyzer\TemplateVariableTypesResolver;
use Symplify\PHPStanRules\Symfony\ValueObject\VariableAndMissingMethodName;
use Symplify\PHPStanRules\Symfony\ValueObject\VariableAndType;
use Twig\Node\ModuleNode;
use Twig\Node\Node;

final class TwigMissingMethodCallAnalyzer
{
    public function __construct(
        private ObjectTypeMethodAnalyzer $objectTypeMethodAnalyzer,
        private TemplateVariableTypesResolver $templateVariableTypesResolver,
        private TwigNodeTraverserFactory $twigNodeTraverserFactory
    ) {
    }

    /**
     * @param ModuleNode<Node> $moduleNode
     * @return VariableAndMissingMethodName[]
     */
    public function resolveFromArrayAndModuleNode(Array_ $array, Scope $scope, ModuleNode $moduleNode): array
    {
        $variableAndTypes = $this->templateVariableTypesResolver->resolveArray($array, $scope);
        $missingMethodCallNodeVisitor = $this->createMissingMethodCallNodeVisitor($variableAndTypes);

        $twigNodeTraverser = $this->twigNodeTraverserFactory->createWithNodeVisitors([$missingMethodCallNodeVisitor]);
        $twigNodeTraverser->traverse($moduleNode);

        return $missingMethodCallNodeVisitor->getVariableAndMissingMethodNames();
    }

    /**
     * @param VariableAndType[] $variableAndTypes
     */
    private function createMissingMethodCallNodeVisitor(array $variableAndTypes): MissingMethodCallNodeVisitor
    {
        return new MissingMethodCallNodeVisitor($variableAndTypes, $this->objectTypeMethodAnalyzer);
    }
}
