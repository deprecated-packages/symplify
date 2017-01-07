# Symplify Rules Overview

## FinalInterfaceSniff (Class)

- Non-abstract class that implements interface should be final.
- Except for Doctrine entities, they cannot be final.

```php
final class SomeClass implements SomeInterface
{
	public function run()
	{

	}
}
```


## BlockPropertyCommentSniff (Commenting)

- Block comment should be used instead of one liner

```php
class SomeClass
{
	/**
	 * @var int
	 */
	public $count;
}
```


## VarPropertyCommentSniff (Commenting)

- Property should have docblock comment.
 
```php
class SomeClass
{
	/**
	 * @var int
	 */
	private $someProperty;
}
```

## MethodCommentSniff (Commenting)

- Method without parameter typehints should have docblock comment.

```php
class SomeClass
{
	/**
	 * @param int $values
	 */
	public function count($values)
	{
	}

    public function count(array $values)
    {
    }
}
```

## MethodReturnTypeSniff (Commenting)

- Getters should have return type (except for {@inheritdoc}).

```php
class SomeClass
{
	/**
	 * @return int
	 */
	public function getResult()
	{
		// ...
	}
}
```


## DebugFunctionCallSniff (Debug)

- Debug functions should not be left in the code


## ClassNamesWithoutPreSlashSniff (Namespaces)

- Class name after new/instanceof should not start with slash

```php
use Some\File;

$file = new File;
```


## AbstractClassNameSniff (Naming)

- Abstract class should have prefix "Abstract"


## InterfaceNameSniff (Naming)

- Interface should have suffix "Interface"
