<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Neon;

use Nette\Neon\Decoder;
use Nette\Neon\Node;
use Nette\Neon\Node\ArrayItemNode;
use Nette\Neon\Node\ArrayNode;
use Nette\Neon\Node\LiteralNode;
use Nette\Neon\Traverser;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NeonClassExtractor
{
    /**
     * @return string[]
     */
    public function extract(SmartFileInfo $fileInfo): array
    {
        $neonDecoder = new Decoder();
        $node = $neonDecoder->parseToNode($fileInfo->getContents());

        $classKeyClassNames = $this->findClassNames($node);
        $stringStaticCallReferences = $this->findsStringStaticCallReferences($node);
        $servicesKeyList = $this->findsServicesKeyList($node);

        return array_merge($classKeyClassNames, $stringStaticCallReferences, $servicesKeyList);
    }

    private function hasKeyValue(ArrayItemNode $arrayItemNode, string $value): bool
    {
        if (! $arrayItemNode->key instanceof LiteralNode) {
            return false;
        }

        return $arrayItemNode->key->toString() === $value;
    }

    /**
     * Finds "class: <name>"
     *
     * @return string[]
     */
    private function findClassNames(Node $node): array
    {
        $classNames = [];

        $traverser = new Traverser();

        $traverser->traverse($node, function (Node $node) use (&$classNames): ?Node {
            if (! $node instanceof ArrayItemNode) {
                return $node;
            }

            if (! $this->hasKeyValue($node, 'class')) {
                return null;
            }

            if ($node->value instanceof LiteralNode) {
                $classNames[] = $node->value->toString();
            }

            return null;
        });

        return $classNames;
    }

    /**
     * Finds <someStatic>::call
     *
     * @return string[]
     */
    private function findsStringStaticCallReferences(Node $node): array
    {
        $classNames = [];

        $traverser = new Traverser();
        $traverser->traverse($node, function (Node $node) use (&$classNames) {
            if (! $node instanceof LiteralNode) {
                return null;
            }

            $stringValue = $node->toString();
            if (substr_count($stringValue, '::') !== 1) {
                return null;
            }

            // service name reference → skip
            if (str_starts_with($stringValue, '@')) {
                return null;
            }

            [$class, $method] = explode('::', $stringValue);
            if (! is_string($class)) {
                return null;
            }

            $classNames[] = $class;
            return null;
        });

        return $classNames;
    }

    /**
     * Finds "services: - <className>"
     *
     * @return string[]
     */
    private function findsServicesKeyList(Node $node): array
    {
        $classNames = [];

        $traverser = new Traverser();
        $traverser->traverse($node, function (Node $node) use (&$classNames) {
            if (! $node instanceof ArrayItemNode) {
                return null;
            }

            if (! $this->hasKeyValue($node, 'services')) {
                return null;
            }

            if (! $node->value instanceof ArrayNode) {
                return null;
            }

            foreach ($node->value->items as $arrayItemNode) {
                if ($arrayItemNode->key !== null) {
                    continue;
                }

                if (! $arrayItemNode->value instanceof LiteralNode) {
                    continue;
                }

                $classNames[] = $arrayItemNode->value->toString();
            }

            return null;
        });

        return $classNames;
    }
}
