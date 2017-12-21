---
title: Configuration
id: 2
---

## Parameters

```yaml
# statie.yml
parameters:
    site_url: http://github.com

    socials:
        facebook: http://facebook.com/github
```

## Importing other configs

It is possible to split long configuration into more smaller files.

```yaml
# statie.yml
imports:
    - { resource: 'data/favorite_links.yml' }

parameters:
    site_url: http://github.com
    socials:
        facebook: http://facebook.com/github
```

```yaml
# data/favorite_links.yml
parameters:
    favorite_links:
        blog:
            name: "Suis Marco"
            url: "http://ocramius.github.io/"
```

## Register Services

```yaml
services:
    App\SomeService: ~

    App\TweetService:
        arguments:
          - '%twitter.api_key%'
```
