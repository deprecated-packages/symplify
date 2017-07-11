# Statie - PHP Static Site Generator

[![Build Status](https://img.shields.io/travis/Symplify/Statie/master.svg?style=flat-square)](https://travis-ci.org/Symplify/Statie)
[![Downloads](https://img.shields.io/packagist/dt/symplify/statie.svg?style=flat-square)](htptps://packagist.org/packages/symplify/statie)


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



### Push content of `/output` to Github pages

To push to e.g. [tomasvotruba/tomasvotruba.cz](https://github.com/TomasVotruba/tomasvotruba.cz) repository, setup repository slug:

```yaml
# statie.neon
parameters:
    github_repository_slug: "TomasVotruba/tomasvotruba.cz"
```

And push it with CLI command:

```
vendor/bin/statie push-to-github-pages tomasvotruba/tomasvotruba.cz --token=${GH_TOKEN}
```

How to setup `${GH_TOKEN}`? Just check [this exemplary .travis.yml](https://github.com/TomasVotruba/tomasvotruba.cz/blob/fddcbe9298ae376145622d735e1408ece447ea09/.travis.yml#L9-L26).

 
## Configuration

### `statie.neon` Config

They way you are used to use Symfony/Nette cofings: single file that allows you to add parameters, include other configs and register services.

So this...

```yaml
# statie.neon
parameters:
    site_url: http://github.com
    socials:
        facebook: http://facebook.com/github
```

...can be used in any template as:

```twig
# source/_layouts/default.latte

<p>Welcome to: {$site_url}</p>

<p>Checkout my FB page: {$socials['facebook']}</p>
```

Need more data in standalone files? Use `includes` section:

```yaml
# statie.neon
includes:
    - data/favorite_links.neon

parameters:
    site_url: http://github.com
    socials:
        facebook: http://facebook.com/github
```

```yaml
# data/favorite_links.neon
parameters:
    favorite_links:
        blog:
            name: "Suis Marco"
            url: "http://ocramius.github.io/"
 ```

Note: [parameter names have to be lowercased](https://github.com/symfony/symfony/issues/23381), due to Symfony\DependencyInjection component. So `basePath` in config is converted to `{$basepath}` in template. That's why I used `base_path` above.

### Modify Post Url Format

To configure post url address just modify:

```yaml
# statie.neon

parameters:
    post_route: blog/:year/:month/:day/:title # default one
    # will produce post detail link: blog/2016/12/01/how-to-host-open-source-blog-for-free
    
    # other examples:
    # :year/:month/:title => 2016/12/how-to-host-open-source-blog-for-free
    # :year/:title => 2016/how-to-host-open-source-blog-for-free
    # blog/:title => blog/how-to-host-open-source-blog-for-free
```

### AMPize whole Website

Let people enjoy your webiste in subways, transatlantic ships and planes with poor wifi connections.
Turn on [AMP](https://www.ampproject.org/):

```yaml
# statie.neon
parameters:
    amp: true
```


### Enable Github-like Headline Anchors

Sharing long post to show specific paragraph is not a sci-fi anymore.

When your hover any headline, an anchor link to it will appear on the left. Click it & share it!

![Headline Anchors](docs/github-like-headline-anchors.png)
 
```yaml
# statie.neon
parameters:   
    markdown_headline_anchors: true 
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


## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
