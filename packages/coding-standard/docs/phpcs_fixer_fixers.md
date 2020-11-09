## DoctrineAnnotationNewlineInNestedAnnotationFixer

Nested object annotations should start on a standalone line

- `Symplify\CodingStandard\Fixer\Annotation\DoctrineAnnotationNewlineInNestedAnnotationFixer`

```php
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user", indexes={@ORM\Index(name="user_id", columns={"another_id"})})
 */
class SomeEntity
{
}
```

:x:

```php
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user", indexes={
 * @ORM\Index(name="user_id", columns={"another_id"})
 * })
 */
class SomeEntity
{
}
```

:+1:

<br>

## RemovePHPStormAnnotationFixer

Remove "Created by PhpStorm" annotations

- `Symplify\CodingStandard\Fixer\Annotation\RemovePHPStormAnnotationFixer`

```php
/**
 * Created by PhpStorm.
 * User: ...
 * Date: 17/10/17
 * Time: 8:50 AM
 */
class SomeClass
{
}
```

:x:

```php
class SomeClass
{
}
```

:+1:

<br>

## ArrayListItemNewlineFixer

Indexed PHP array item has to have one line per item

- `Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer`

```php
$value = ['simple' => 1, 'easy' => 2];
```

:x:

```php
$value = ['simple' => 1,
'easy' => 2];
```

:+1:

<br>

## ArrayOpenerAndCloserNewlineFixer

Indexed PHP array opener [ and closer ] must be on own line

- `Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer`

```php
$items = [1 => 'Hey'];
```

:x:

```php
$items = [
1 => 'Hey'
];
```

:+1:

<br>

## StandaloneLineInMultilineArrayFixer

Indexed arrays must have 1 item per line

- `Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer`

```php
$friends = [1 => 'Peter', 2 => 'Paul'];
```

:x:

```php
$friends = [
    1 => 'Peter',
    2 => 'Paul'
];
```

:+1:

<br>

## ParamReturnAndVarTagMalformsFixer

Fixes @param, @return, @var and inline @var annotations broken formats

- `Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer`

```php
/**
 * @param string
 */
function getPerson($name)
{
}
```

:x:

```php
/**
 * @param string $name
 */
function getPerson($name)
{
}
```

:+1:

<br>

## LineLengthFixer

Array items, method parameters, method call arguments, new arguments should be on same/standalone line to fit line length.

- `Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer`

```php
function some($veryLong, $superLong, $oneMoreTime)
{
}

function another(
    $short,
    $now
) {
}
```

:x:

```php
function some(
    $veryLong,
    $superLong,
    $oneMoreTime
) {
}

function another($short, $now) {
}
```

:+1:

<br>

## StandardizeHereNowDocKeywordFixer

Use configured nowdoc and heredoc keyword

- `Symplify\CodingStandard\Fixer\Naming\StandardizeHereNowDocKeywordFixer`

```php
$value = <<<'WHATEVER'
...
'WHATEVER'
```

:x:

```php
$value = <<<'CODE_SNIPPET'
...
'CODE_SNIPPET'
```

:+1:

<br>

## MethodChainingNewlineFixer

Each chain method call must be on own line

- `Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer`

```php
$someClass->firstCall()->secondCall();
```

:x:

```php
$someClass->firstCall()
->secondCall();
```

:+1:

<br>

## SpaceAfterCommaHereNowDocFixer

Add space after nowdoc and heredoc keyword, to prevent bugs on PHP 7.2 and lower, see https://laravel-news.com/flexible-heredoc-and-nowdoc-coming-to-php-7-3

- `Symplify\CodingStandard\Fixer\Spacing\SpaceAfterCommaHereNowDocFixer`

```php
$values = [
    <<<RECTIFY
Some content
RECTIFY,
    1000
];
```

:x:

```php
$values = [
    <<<RECTIFY
Some content
RECTIFY
,
    1000
];
```

:+1:

<br>

## BlankLineAfterStrictTypesFixer

Strict type declaration has to be followed by empty line

- `Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer`

```php
declare(strict_types=1);
namespace App;
```

:x:

```php
declare(strict_types=1);

namespace App;
```

:+1:

<br>

## CommentedOutCodeSniff

There should be no commented code. Git is good enough for versioning

- `Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff`

```php
// $one = 1;
// $two = 2;
// $three = 3;
```

:x:

```php
// note
```

:+1:

<br>
