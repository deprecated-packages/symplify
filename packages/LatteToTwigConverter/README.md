# Latte to Twig Converter

[![Build Status](https://img.shields.io/travis/Symplify/LatteToTwigConverter/master.svg?style=flat-square)](https://travis-ci.org/Symplify/LatteToTwigConverter)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/latte-to-twig-converter.svg?style=flat-square)](https://packagist.org/packages/symplify/latte-to-twig-converter)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%latte-to-twig-converter)

Do you want to turn your [Latte](https://latte.nette.org/en/) templates to [Twig](https://twig.symfony.com/)?


**Before**

```html
{foreach $values as $key => $value}
    {$value->getName()} 
    
    {if isset($value['position'])}
        {$value['position']|noescape}
    {else}
        {var $noPosition = true}
    {/if}
{/foreach}
```

**After**

```twig
{% for key, value in values %}
    {{ value.getName() }}
    
    {% if value.position is defined %}
        {{ value.position|raw }}
    {% else %}
        {% set noPosition = true %}
    {% endif %}
{% endfor %}
```

And much more!

This package won't do it all for you, but **it will help you with 80 % of the boring work**.

## Install

```bash
composer require symplify/latte-to-twig-converter --dev
```

## Usage

It scan all the `*.twig` files and if it founds Latte syntax in it, it'll convert it to Twig.
That way you can keep `*.latte` files you need.


```bash
vendor/bin/latte-to-twig-converter convert /directory
```

That's it :)