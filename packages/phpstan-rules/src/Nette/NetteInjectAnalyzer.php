<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use Symplify\PHPStanRules\PhpDoc\BarePhpDocParser;

final class NetteInjectAnalyzer
{
    /**
     * @var BarePhpDocParser
     */
    private $barePhpDocParser;

    public function __construct(BarePhpDocParser $barePhpDocParser)
    {
        $this->barePhpDocParser = $barePhpDocParser;
    }

    public function isInjectProperty(Property $property): bool
    {
        if (! $property->isPublic()) {
            return false;
        }

        return $this->hasInjectAnnotation($property);
    }

    public function isInjectClassMethod(ClassMethod $classMethod): bool
    {
        if (! $classMethod->isPublic()) {
            return false;
        }

        $methodName = $classMethod->name->toString();
        if (Strings::startsWith($methodName, 'inject')) {
            return true;
        }

        return $this->hasInjectAnnotation($classMethod);
    }

    private function hasInjectAnnotation(Node $node): bool
    {
        $phpDocTagNodes = $this->barePhpDocParser->parseNodeToPhpDocTagNodes($node);
        foreach ($phpDocTagNodes as $phpDocTagNode) {
            if ($phpDocTagNode->name === '@inject') {
                return true;
            }
        }

        return false;
    }
}
