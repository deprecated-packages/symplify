# Statie - Modern and Simple Static Site Generator in PHP

[![Build Status](https://img.shields.io/travis/Symplify/Statie/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Statie)
[![Downloads](https://img.shields.io/packagist/dt/symplify/statie.svg?style=flat-square)](htptps://packagist.org/packages/symplify/statie)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fstatie)

Statie takes HTML, Markdown and Latte files and generates static HTML page.


## Install via Composer

```bash
composer require symplify/statie
```

## And via Node

```bash
npm install -g gulp gulp-watch child_process
```

## Usage

### Generate content from `/source` to `/output` in HTML

```bash
vendor/bin/statie generate source
```

### See Generated web

```bash
php -S localhost:8000 -t output
```

And open [localhost:8000](http://localhost:8000) in browser.

### Live Rebuild

For live rebuild, just add `gulpfile.js`:

```javascript
var gulp = require('gulp');
var watch = require('gulp-watch');
var exec = require('child_process').exec;

gulp.task('default', function () {
    // Run local server, open localhost:8000 in your browser
    exec('php -S localhost:8000 -t output');

    return watch(['source/**/*', '!**/*___jb_tmp___'], { ignoreInitial: false })
        // For the second arg see: https://github.com/floatdrop/gulp-watch/issues/242#issuecomment-230209702
        .on('change', function() {
            exec('vendor/bin/statie generate source', function (err, stdout, stderr) {
                console.log(stdout);
                console.log(stderr);
            });
        });
});
```

And run:

```bash
gulp
```


## Configuration

### `statie.yml ` Config

This is basically `config.yml` Symfony Kernel that you know from Symfony apps. You can.

**1. [Add Parameters](https://symfony.com/doc/current/service_container/parameters.html)**

```yaml
# statie.yml
parameters:
    site_url: http://github.com

    socials:
        facebook: http://facebook.com/github
```

...that are available in every template:

```twig
# source/_layouts/default.latte

<p>Welcome to: {$site_url}</p>

<p>Checkout my FB page: {$socials['facebook']}</p>
```

**2. [Import other configs](http://symfony.com/doc/current/service_container/import.html)**

```yaml
# statie.yml
imports:
    - { resource: 'data/favorite_links.yml' }

parameters:
    site_url: http://github.com
    socials:
        facebook: http://facebook.com/github
```

...and split long configuration into more smaller files:

```yaml
# data/favorite_links.yml
parameters:
    favorite_links:
        blog:
            name: "Suis Marco"
            url: "http://ocramius.github.io/"
```

**3. And [Register Services](https://symfony.com/doc/current/service_container.html)**

```yaml
services:
    App\SomeService: ~
   
    App\TweetService:
        arguments:
          - '%twitter.api_key%'
```


## Detailed Documentation

- [Hook to Statie Application cycle with Events](/docs/HookToStatie.md)
- [Add Headline Anchor Links](/docs/HeadlineAnchors.md)
- [Add Related Items](/docs/RelatedItems.md)
- [Push Content to Github Pages with Travis](/docs/PushContentToGithubPagesWithTravis.md)


### Generator Elements

All items that **contain multiple records and need own html page** - e.g. posts - can be configured in `statie.yml`:  

```yml
parameters:
    generators:
        # key name, nice to have for more informative error reports
        posts:
            # required parameters
         
            # name of variable inside single such item
            variable: post
            # name of variable that contains all items
            varbiale_global: posts
            # directory, where to look for them
            path: '_posts' 
            # which layout to use
            layout: '_layouts/@post.latte' 
            # and url prefix, e.g. /blog/some-post.md
            route_prefix: 'blog'
             
            # optional parameters
             
            # an object that will wrap it's logic, you can add helper methods into it and use it in templates
            # Symplify\Statie\Renderable\File\File is used by default
            object: 'Symplify\Statie\Renderable\File\PostFile' 
```



### Custom Output Path

Default output path for files is `<filename>/index.html`. That makes url nice and short.

In case you need a different path, use `outputPath` key in the configuration of the file.

E.g. running [Github Pages and 404 page](https://help.github.com/articles/creating-a-custom-404-page-for-your-github-pages-site/).

```html
---
layout: default
title: "Missing page, missing you"
outputPath: "404.html"
---

{block content}
    ...
{/block}
```


## Runs on Statie

On there website you can find inspiration, how to use it!

- [github.com/tomasvotruba/tomasvotruba.cz](https://github.com/tomasvotruba/tomasvotruba.cz)
- [github.com/pehapkari/pehapkari.cz](https://github.com/pehapkari/pehapkari.cz)
- [github.com/enumag/enumag.cz](https://github.com/pehapkarienumag.cz)
- [github.com/ikvasnicaikvasnica.com](https://github.com/ikvasnica/ikvasnica.com)


## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
