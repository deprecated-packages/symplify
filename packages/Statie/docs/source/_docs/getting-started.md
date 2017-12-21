---
title: Getting Started
id: 1
---

## Install

```bash
composer require symplify/statie
```

## How to Generate and See the Website?

Prepare content for Statie... . Simple 'index.latte' would do for start, but you can also inspire in [tomasvotruba.cz personal website](https://github.com/TomasVotruba/tomasvotruba.cz/tree/master/source).

Generate static site from `/source` (argument) to `/output` (default value) in HTML:

```bash
vendor/bin/statie generate source
```

Run local PHP server

```bash
php -S localhost:8000 -t output
```

And see web in browser [localhost:8000](http://localhost:8000).
