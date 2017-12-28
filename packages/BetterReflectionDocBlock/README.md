# Better Reflection DocBlock

[![Build Status](https://img.shields.io/travis/Symplify/BetterReflectionDocBlock/master.svg?style=flat-square)](https://travis-ci.org/Symplify/BetterReflectionDocBlock)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fbetter-reflection-docblock)


Slim wrapper around [phpdocumentor/reflection-docblock](https://github.com/phpDocumentor/ReflectionDocBlock) **with better DX, simpler API** and this features:

- accepts invalid `@param` and `@return` annotations instead of throwing exceptions
- differentiates between `mixed[]` and `array` types
- fixes redundant empty space while saving the docblock to string
- does not add extra pre-slash while saving the docblock - `@param \SomeClass`


## Install

```bash
composer require symplify/better-reflection-docblock
```
