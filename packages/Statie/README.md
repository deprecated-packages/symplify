# Statie - PHP Static Site Generator

[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]
[![Latest Version on Packagist][ico-version]][link-packagist]


Sculpin takes **Markdown files** and combines them with **Twig templates** to produce a set of static HTML files.

## Install via Composer

```
composer require symplify/statie
```

## Usage

### Base commands

#### Generate content from `/source` to `/output` in HTML

```
vendor/bin/sculpin generate
vendor/bin/sculpin generate --server
```

#### Push content of `/output` to Github pages

```
vendor/bin/sculpin push-to-github-pages
```

### Configuration

#### Global variables

EVERY `.neon` or `.yaml` found in `/source` directory is loaded to global variables.
You can store variables, lists of data etc.

So this...

```yaml
# config/config.neon
siteUrl: http://github.com
socials:
    facebook: http://facebook.com/github
```

...can be displayed in any template as:

```twig
# _layouts/default.latte
<p>Welcome to: {$siteUrl}</p>

<p>Checkout my FB page: {$socials['facebook']}</p>
```

#### Special configuration

To configure post url address just modify:

```yaml
# config/config.neon
configuration:
    postRoute: blog/:year/:month/:day/:title # default one
    # will produce post detail link: blog/2016/12/01/how-to-host-open-source-blog-for-free
    
    # other examples:
    # :year/:month/:title => 2016/12/how-to-host-open-source-blog-for-free
    # :year/:title => 2016/how-to-host-open-source-blog-for-free
    # blog/:title => blog/how-to-host-open-source-blog-for-free
```


[ico-version]: https://img.shields.io/packagist/v/Symplify/Statie.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Symplify/Statie/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/Symplify/Statie.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Symplify/Statie.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/Symplify/Statie.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/Symplify/Statie
[link-travis]: https://travis-ci.org/Symplify/Statie
[link-scrutinizer]: https://scrutinizer-ci.com/g/Symplify/Statie/code-structure/master?elementType=class&orderField=test_coverage&order=asc&changesExpanded=0
[link-code-quality]: https://scrutinizer-ci.com/g/Symplify/Statie/code-structure/master/hot-spots
[link-downloads]: https://packagist.org/packages/symplify/statie/stats
