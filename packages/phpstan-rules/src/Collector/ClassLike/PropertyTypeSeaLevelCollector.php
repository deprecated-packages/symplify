<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\ClassLike;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * @implements Collector<ClassLike, array{int, int, string}>>
 */
final class PropertyTypeSeaLevelCollector implements Collector
{
    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    /**
     * @param ClassLike $node
     * @return array{int, int, string}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $printedProperties = '';

        // return typed properties/all properties
        $propertyCount = count($node->getProperties());

        $typedPropertyCount = 0;

        $standard = new Standard();

        foreach ($node->getProperties() as $property) {
            if ($property->type instanceof Node) {
                ++$typedPropertyCount;
                continue;
            }

            $docComment = $property->getDocComment();
            if ($docComment instanceof Doc) {
                $docCommentText = $docComment->getText();

                // skip as unable to type
                if (str_contains($docCommentText, 'callable') || str_contains($docCommentText, 'resource')) {
                    ++$typedPropertyCount;
                    continue;
                }
            }

            // give useful context
            $printedProperties .= PHP_EOL . PHP_EOL . $standard->prettyPrint([$property]);
        }

        return [$typedPropertyCount, $propertyCount, $printedProperties];
    }
}
