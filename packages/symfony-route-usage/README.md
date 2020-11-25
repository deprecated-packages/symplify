# Detect Used and Unused Symfony Routes

[![Downloads total](https://img.shields.io/packagist/dt/symplify/symfony-route-usage.svg?style=flat-square)](https://packagist.org/packages/symplify/symfony-route-usage/stats)

**Read about this package: [How to Find Dead Symfony Routes](https://www.tomasvotruba.com/blog/2020/04/06/how-to-find-dead-symfony-routes/)**

<br>

*Inspired by [Route Usage](https://github.com/julienbourdeau/route-usage/) for Laravel:*

"This package keeps track of all requests to know what controller method, and when it was called. The goal is not to build some sort of analytics but to find out if there are unused endpoints or controller method.

After a few years, any projects have dead code and unused endpoint. Typically, you removed a link on your frontend, nothing ever links to that old /special-page. You want to remove it, but you're not sure. Have look at the route_usage table and figure out when this page was accessed for the last time. Last week? Better keep it for now. 3 years ago? REMOVE THE CODE!"

## Install

```bash
composer require symplify/symfony-route-usage
```

Register bundle to your `config/bundles.php` (in case Flex misses it):

```php
return [
    Symplify\SymfonyRouteUsage\SymfonyRouteUsageBundle::class => [
        'all' => true,
    ],
];
```

## Usage

Show used routes:

```bash
bin/console show-route-usage
```

<div align="center">
    <img src="/docs/used_routes.png">
</div>

<br>

Show dead routes:

```bash
bin/console show-dead-routes
```

<div align="center">
    <img src="/docs/dead_routes.png">
</div>

## Configuration

By default, `_*` and `error_controller` is excluded. If you want to exclude more routes, use regex parameter:

```yaml
# config/services.yaml
parameters:
    route_usage_exclude_route_regex: '#legacy#'
```

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [migrify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the migrify monorepo. We welcome contributions for this package on [symplify/migrify](https://github.com/symplify/migrify).
