# PHPStan Rules - Cognitive Complexity

[Why Cognitive Complexity is the best rule in your coding standard?](https://www.tomasvotruba.com/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/)

## Cognitive Complexity for Method and Class Must be Less than X

- class: [`FunctionLikeCognitiveComplexityRule`](../packages/cognitive-complexity/src/Rules/FunctionLikeCognitiveComplexityRule.php)
- class: [`ClassLikeCognitiveComplexityRule`](../packages/cognitive-complexity/src/Rules/ClassLikeCognitiveComplexityRule.php)

```yaml
# phpstan.neon
includes:
    - vendor/symplify/coding-standard/packages/cognitive-complexity/config/cognitive-complexity-services.neon

services:
    -
        class: Symplify\PHPStanRules\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule
        tags: [phpstan.rules.rule]
        arguments:
            maxMethodCognitiveComplexity: 8

    -
        class: Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule
        tags: [phpstan.rules.rule]
        arguments:
            maxClassCognitiveComplexity: 50
```

```php
<?php declare(strict_types=1);

class SomeClass
{
    public function simple($value)
    {
        if ($value !== 1) {
            if ($value !== 2) {
                if ($value !== 3) {
                    return false;
                }
            }
        }

        return true;
    }
}
```

:x:

```php
<?php declare(strict_types=1);

class SomeClass
{
    public function simple($value)
    {
        if ($value === 1) {
            return true;
        }

        if ($value === 2) {
            return true;
        }

        return $value === 3;
    }
}
```

:+1:
