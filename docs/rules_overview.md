## ClassLikeCognitiveComplexityRule

Cognitive complexity of class/trait must be under specific limit

- `Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule`

```php
class SomeClass
{
    public function simple($value)
    {
        if ($value !== 1) {
            if ($value !== 2) {
                return false;
            }
        }

        return true;
    }

    public function another($value)
    {
        if ($value !== 1 && $value !== 2) {
            return false;
        }

        return true;
    }
}
```
```


```php
class SomeClass
{
    public function simple($value)
    {
        return $this->someOtherService->count($value);
    }

    public function another($value)
    {
        return $this->someOtherService->delete($value);
    }
}
```


## FunctionLikeCognitiveComplexityRule

Cognitive complexity of function/method must be under specific limit

- `Symplify\PHPStanRules\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule`

```php
class SomeClass
{
    public function simple($value)
    {
        if ($value !== 1) {
            if ($value !== 2) {
                return false;
            }
        }

        return true;
    }
}
```
```


```php
class SomeClass
{
    public function simple($value)
    {
        if ($value === 1) {
            return true;
        }

        return $value === 2;
    }
}
```
