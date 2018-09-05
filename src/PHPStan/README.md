# PHPStan Extensions

## Install

```yaml
# phpstan.neon
includes:
    - 'src/PHPStan/config/config.neon'
```

## 1. Stats Formatter - the Best Way to Start with PHPStan

Do you have zillion errors in you project? That's common... and frustrating. Why not start with the most wide-spread errors? **Solve one type of problem to get rid of dozens of errors**.

Run:

```bash
vendor/bin/phpstan analyse src --level max --error-format stats
```

to get this nice overview of top 10 errors:

```bash
These are 10 most frequent errors
=================================

 -------------------------------------------------------------------------------------------------------------- ------- 
  Message                                                                                                        Count  
 -------------------------------------------------------------------------------------------------------------- ------- 
  Call to an undefined method object::getCheckers().                                                             7x     
  Strict comparison using === between array and null will always evaluate to false.                              4x     
  Cannot call method getCategory() on Symplify\ChangelogLinker\ChangeTree\Change|null.                           4x     
  Cannot call method getPackage() on Symplify\ChangelogLinker\ChangeTree\Change|null.                            4x     
  Parameter #1 $object_or_string of function is_a expects object|string, string|null given.                      3x     
  Casting to array something that's already array.                                                               3x     
  Parameter #2 $searchIndex of method PhpCsFixer\Tokenizer\Tokens::findBlockEnd() expects int, int|null given.   3x     
  Parameter #2 $items of method PhpCsFixer\Tokenizer\Tokens::insertAt() expects                                  3x     
  (iterable<PhpCsFixer\Tokenizer\Token>&PhpCsFixer\Tokenizer\Tokens)|PhpCsFixer\Tokenizer\Token,                        
  array<PhpCsFixer\Tokenizer\Token> given.                                                                              
  Parameter #1 $string of function strlen expects string, string|false given.                                    3x     
  Negated boolean is always false.                                                                               3x     
 -------------------------------------------------------------------------------------------------------------- ------- 
```

## 2. Ignore Formatter

Do you need to ignore few errors but don't want to play with regex? Run:

```bash
vendor/bin/phpstan analyse src --level max --error-format ignore
```

to get it on silver plate, ready for copy-paste: 

```bash

Add to "parameters > ignoreErrors" section in "phpstan.neon"
============================================================

# phpstan.neon
parameters:
    ignoreErrors:
        - '#Parameter \#1 \$errors of method Symplify\\PHPStan\\Error\\ErrorGrouper\:\:groupErrorsToMessagesToFrequency\(\) expects array<Symplify\\EasyCodingStandard\\Error\\Error\>, array<PHPStan\\Analyser\\Error\> given#' # found 2x
```
