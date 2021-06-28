# Simple PHP Doc Parser

Simple service integration of phpstan/phpdoc-parser, with few extra goodies for practical use

## 1. Install

```bash
composer require symplify/simple-php-doc-parser
```

## 2. Register Bundle

Register bundle in your project:

```php
// app/bundles.php
return [
    Symplify\SimplePhpDocParser\Bundle\SimplePhpDocParserBundle::class => [
        'all' => true,
    ],
];
```

or via Kernel:

```php
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\SimplePhpDocParser\Bundle\SimplePhpDocParserBundle;

final class AppKernel extends Kernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new SimplePhpDocParserBundle()];
    }
}
```

## 3. Usage of `SimplePhpDocParser`

Required services `Symplify\SimplePhpDocParser\SimplePhpDocParser` in constructor, where you need it, and use it:

```php
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Symplify\SimplePhpDocParser\SimplePhpDocParser;

final class SomeClass
{
    /**
     * @var SimplePhpDocParser
     */
    private $simplePhpDocParser;

    public function __construct(SimplePhpDocParser $simplePhpDocParser)
    {
        $this->simplePhpDocParser = $simplePhpDocParser;
    }

    public function some(): void
    {
        $docBlock = '/** @param int $name */';

        /** @var PhpDocNode $phpDocNode */
        $simplePhpDocNode = $this->simplePhpDocParser->parseDocBlock($docBlock);

        // param extras

        /** @var TypeNode $nameParamType */
        $nameParamType = $simplePhpDocNode->getParamType('name');

        /** @var ParamTagValueNode $nameParamTagValueNode */
        $nameParamTagValueNode = $simplePhpDocNode->getParam('name');
    }
}
```

## 4. Traverse Nodes with `PhpDocNodeTraverser`

```php
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use Symplify\SimplePhpDocParser\PhpDocNodeTraverser;
use Symplify\SimplePhpDocParser\PhpDocNodeVisitor\AbstractPhpDocNodeVisitor;
use Symplify\SimplePhpDocParser\PhpDocNodeVisitor\CallablePhpDocNodeVisitor;

$phpDocNodeTraverser = new PhpDocNodeTraverser();
$phpDocNode = new PhpDocNode([new PhpDocTagNode('@var', new VarTagValueNode(new IdentifierTypeNode('string')))]);

// A. you can use callable to traverse
$callable = function (Node $node): Node {
    if (!$node instanceof VarTagValueNode) {
        return $node;
    }

    $node->type = new IdentifierTypeNode('int');
    return $node;
};

$callablePhpDocNodeVisitor = new CallablePhpDocNodeVisitor($callable);
$phpDocNodeTraverser->addPhpDocNodeVisitor($callablePhpDocNodeVisitor);

// B. or class that extends AbstractPhpDocNodeVisitor
final class IntegerPhpDocNodeVisitor extends AbstractPhpDocNodeVisitor
{
    public function enterNode(Node $node): ?Node
    {
        if (!$node instanceof VarTagValueNode) {
            return $node;
        }

        $node->type = new IdentifierTypeNode('int');
        return $node;
    }
}

$integerPhpDocNodeVisitor = new IntegerPhpDocNodeVisitor();
$phpDocNodeTraverser->addPhpDocNodeVisitor($integerPhpDocNodeVisitor);

// then traverse the main node
$phpDocNodeTraverser->traverse($phpDocNode);
```
