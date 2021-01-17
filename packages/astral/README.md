# Astral - Toolking for smart daily work with AST

[![Downloads total](https://img.shields.io/packagist/dt/symplify/astral.svg?style=flat-square)](https://packagist.org/packages/symplify/astral/stats)

## Install

```bash
composer require symplify/astral
```

### Add to Symfony Project

Register bundle in `config/bundles.php`:

```php
return [
    Symplify\Astral\Bundle\AstralBundle::class => [
        'all' => true,
    ],
];
```

### Add to PHPStan Rules

Include in your `phpstan.neon`:

```yaml
includes:
    - vendor/symplify/astral/config/services.neon
```

## Usage

### 1. Resolve Name of Any Node

How can you get name of speficic node?

```php
$someObject->someMethodCall();
// "someObject"
// "someMethodCall"
```

Require `SimpleNameResolver` in any service:

```php
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use Symplify\Astral\Naming\SimpleNameResolver;

final class SomeRule
{
    public function __construct(
        // PHP 8.0 promoted property syntax
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function process(Node $node)
    {
        if ($node instanceof MethodCall) {
            $callerName = $this->simpleNameResolver->getName($node->var);
            $methodName = $this->simpleNameResolver->getName($node->name);
        }
    }
}
```

For dynamic names that are not possible to resolve, the `null` will be returned:

```php
$variable->${someMethod}();
```

### 2. Resolve Value of Node

```php
$value = 1000;
```

How can we get the `1000` value?

```php
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Scalar\LNumber;
use PHPStan\Analyser\Scope;
use Symplify\Astral\NodeValue\NodeValueResolver;

final class SomeRule
{
    public function __construct(
        // PHP 8.0 promoted property syntax
        private NodeValueResolver $nodeValueResolver
    ) {
    }

    public function process(Node $node, Scope $scope)
    {
        if ($node instanceof Assign && $node->expr instanceof LNumber) {
            $resolvedValue = $this->nodeValueResolver->resolve($node->expr, $scope->getFile());
        }
    }
}
```

Work for static expressions like these:

```php
$value = 'Hey';
// "Hey"

SomeClass::class;
// "SomeClass"

class SomeClass
{
    public const VALUE = 'different';
}

SomeClass::VALUE;
// "different"

__DIR__;
// realpath of the __DIR__ in its place
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

<br>

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
