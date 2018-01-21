# Better Reflection DocBlock

[![Build Status](https://img.shields.io/travis/Symplify/BetterReflectionDocBlock/master.svg?style=flat-square)](https://travis-ci.org/Symplify/BetterReflectionDocBlock)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fbetter-reflection-docblock)

Slim wrapper around [phpdocumentor/reflection-docblock](https://github.com/phpDocumentor/ReflectionDocBlock) **with better DX, simpler API** and this features:

- Accepts invalid `@param` and `@return` annotations instead of throwing exceptions

    **Original**

    ```php
    /**
     * @param \string
     */
    ```

    **Reflection DocBlock**

    - `throws Exception`

    **Better Reflection DocBlock**

    - :+1:

- Differentiates [between `mixed[]` and `array` types](https://github.com/phpDocumentor/TypeResolver/pull/48)

    **Original**

    ```php
    /**
     * @param array $value
     * @return mixed[]
     */
    ```

    **Reflection DocBlock**

    ```php
    /**
     * @param array $value
     * @return array
     */
    ```

    **Better Reflection DocBlock**

    ```php
    /**
     * @param array $value
     * @return mixed[]
     */
    ```

- Fixes [redundant empty space while saving the docblock to string](https://github.com/phpDocumentor/ReflectionDocBlock/pull/138)
- Does not add extra pre-slash while saving the docblock - `@param \SomeClass`

    **Original**

    ```php
    /**
     * @param Type|AnotherType
     * @return Type
     * @throw \AnotherType
     */
    ```

    **Reflection DocBlock**

    ```php
    /**
     * @param \Type|\AnotherType
     * @return \Type
     * @throw \AnotherType
     */
    ```

    **Better Reflection DocBlock**

    ```php
    /**
     * @param Type|AnotherType
     * @return Type
     * @throw \AnotherType
     */
    ```

- Respects empty lines between tags

    **Original**

    ```php
    /**
     * @param string $value
     *
     * @return int
     */
    ```

    **Reflection DocBlock**

    ```php
    /**
     * @param string $value
     * @return int
     */
    ```

    **Better Reflection DocBlock**

    ```php
    /**
     * @param string $value
     *
     * @return int
     */
    ```

## Install

```bash
composer require symplify/better-reflection-docblock
```
