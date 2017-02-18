# Statie - PHP Static Site Generator

[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]
[![Latest Version on Packagist][ico-version]][link-packagist]


Statie takes HTML, Markdown and Latte files and generates static HTML page.

## Install via Composer

```bash
composer require symplify/statie
```

## And via Node

```bash
npm install -g gulp gulp-watch
```

## Usage

### Generate content from `/source` to `/output` in HTML

```bash
vendor/bin/statie generate
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

    return watch(['source/*', '!**/*___jb_tmp___'], { ignoreInitial: false })
        // For the second arg see: https://github.com/floatdrop/gulp-watch/issues/242#issuecomment-230209702
        .on('change', function() {
            exec('vendor/bin/statie generate', function (err, stdout, stderr) {
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



### Push content of `/output` to Github pages

To push to e.g. [tomasvotruba/tomasvotruba.cz](https://github.com/TomasVotruba/tomasvotruba.cz) repository, call this:

```
vendor/bin/statie push-to-github-pages tomasvotruba/tomasvotruba.cz --token=${GH_TOKEN}
```

How to setup `${GH_TOKEN}`? Just check [this exemplary .travis.yml](https://github.com/TomasVotruba/tomasvotruba.cz/blob/fddcbe9298ae376145622d735e1408ece447ea09/.travis.yml#L9-L26).

 
## Configuration

### Global variables

All `.neon` files found in `/source` directory are loaded to global variables.
You can store variables, lists of data etc.

So this...

```yaml
# source/_config/config.neon
siteUrl: http://github.com
socials:
    facebook: http://facebook.com/github
```

...can be displayed in any template as:

```twig
# source/_layouts/default.latte

<p>Welcome to: {$siteUrl}</p>

<p>Checkout my FB page: {$socials['facebook']}</p>
```

### Modify Post Url Format

To configure post url address just modify:

```yaml
# source/_config/config.neon

configuration:
    postRoute: blog/:year/:month/:day/:title # default one
    # will produce post detail link: blog/2016/12/01/how-to-host-open-source-blog-for-free
    
    # other examples:
    # :year/:month/:title => 2016/12/how-to-host-open-source-blog-for-free
    # :year/:title => 2016/how-to-host-open-source-blog-for-free
    # blog/:title => blog/how-to-host-open-source-blog-for-free
```


### Enable Github-like Headline Anchors

When a headline is hovered, an anchor link to it will appear on the left.

![Headline Anchors](docs/github-like-headline-anchors.png)
 
```yaml
# source/_config/config.neon
configuration:   
    markdownHeadlineAnchors: FALSE # default one
    # TRUE will enable Github-like anchored headlines for *.md files     
```

You can use this sample css and modify it to your needs:

```css
/* anchors for post headlines */
.anchor {
    padding-right: .3em;
    float: left;
    margin-left: -.9em;
}

.anchor, .anchor:hover {
    text-decoration: none;
}

h1 .anchor .anchor-icon, h2 .anchor .anchor-icon, h3 .anchor .anchor-icon {
    visibility: hidden;
}

h1:hover .anchor-icon, h2:hover .anchor-icon, h3:hover .anchor-icon {
    visibility: inherit;
}

.anchor-icon {
    display: inline-block;
}
```


[ico-version]: https://img.shields.io/packagist/v/Symplify/Statie.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Symplify/Statie/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/Symplify/Statie.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/Symplify/Statie.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/Symplify/Statie
[link-travis]: https://travis-ci.org/Symplify/Statie
[link-scrutinizer]: https://scrutinizer-ci.com/g/Symplify/Statie/code-structure/master?elementType=class&orderField=test_coverage&order=asc&changesExpanded=0
[link-downloads]: https://packagist.org/packages/symplify/statie/stats
