# Statie - Modern and Simple Static Site Generator in PHP

[![Build Status](https://img.shields.io/travis/Symplify/Statie/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Statie)
[![Downloads](https://img.shields.io/packagist/dt/symplify/statie.svg?style=flat-square)](htptps://packagist.org/packages/symplify/statie)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fstatie)

Statie takes HTML, Markdown and Latte files and generates static HTML page.


## Install

```bash
composer require symplify/statie
```

## How to Generate and See the Website?

1. Prepare content for Statie... . Simple 'index.latte' would do for start, but you can also inspire in [tomasvotruba.cz personal website](https://github.com/TomasVotruba/tomasvotruba.cz/tree/master/source).


2. Generate static site from `/source` (argument) to `/output` (default value) in HTML:

```bash
vendor/bin/statie generate source
```

3. Run local PHP server

```bash
php -S localhost:8000 -t output
```

4. And see web in browser [localhost:8000](http://localhost:8000).


## Live Rebuild

```bash
npm install -g gulp gulp-watch child_process
```

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


## Documentation

- [Add Headline Anchor Links](/docs/HeadlineAnchors.md)
- [Add Related Items](/docs/RelatedItems.md)
- [Push Content to Github Pages with Travis](/docs/PushContentToGithubPagesWithTravis.md)
- [Custom File Output Path](/docs/CustomOutputPath.md)

### Extending Statie

- [Hook to Statie Application cycle with Events](/docs/HookToStatie.md)
- [Add Generator like Posts](/docs/Generators.md)



## Who Runs on Statie?

See what Statie can do and how community uses it:

- [github.com/tomasvotruba/tomasvotruba.cz](https://github.com/tomasvotruba/tomasvotruba.cz)
- [github.com/pehapkari/pehapkari.cz](https://github.com/pehapkari/pehapkari.cz)
- [github.com/crazko/romanvesely.com](https://github.com/crazko/romanvesely.com)
- [github.com/ikvasnica/ikvasnica.com](https://github.com/ikvasnica/ikvasnica.com)
- [github.com/enumag/enumag.cz](https://github.com/enumag/enumag.cz)
- [posobota.cz](https://www.posobota.cz/)


**Do you run on Statie too?** Let the world know and [send PR to add your website here](https://github.com/Symplify/Symplify/edit/master/packages/Statie/README.md).


## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
