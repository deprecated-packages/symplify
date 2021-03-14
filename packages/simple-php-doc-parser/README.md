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
        $phpDocNode = $this->simplePhpDocParser->parseDocBlock($docBlock);

        // param extras

        /** @var TypeNode $nameParamType */
        $nameParamType = $phpDocNode->getParamType('name');

        /** @var ParamTagValueNode $nameParamTagValueNode */
        $nameParamTagValueNode = $phpDocNode->getParam('name');
    }
}
```

## 4. Traverse Nodes with `PhpDocNodeTraverser`

```php
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\SimplePhpDocParser\PhpDocNodeTraverser;

$phpDocNodeTraverser = new PhpDocNodeTraverser();


$node = // ...any node;
$docContent = '/** @var string */';

$phpDocNodeTraverser->traverseWithCallable($node, $docContent, function (Node $node, string $docContent): Node {
    if ($node instanceof UnionTypeNode) {
        return $node;
    }

    // do some operation on $node
    return $node;
});
```
