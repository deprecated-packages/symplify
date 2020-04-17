# Symfony Static Dumper

[![Downloads total](https://img.shields.io/packagist/dt/symplify/symfony-static-dumper.svg?style=flat-square)](https://packagist.org/packages/symplify/symfony-static-dumper/stats)

Dump your Symfony app to HTML + CSS + JS only static website.
Useful for deploy to Github Pages and other non-PHP static website hostings.

## Install

```bash
composer require symplify/symfony-static-dumper
```

Add to `config/bundles.php` if you're not using Flex:

```php
return [
    Symplify\SymfonyStaticDumper\SymfonyStaticDumperBundle::class => ['all' => true],
];
```

## Use

```bash
bin/console dump-static-site
```

The website will be generated to `/output` directory in your root project.

To see the website, just run local server:

```bash
php -S localhost:8001 -t output
```

And open [localhost:8001](http://localhost:8001/) in your browser.
