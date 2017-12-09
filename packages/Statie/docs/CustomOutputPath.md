## Custom Output Path

Default output path for files is `<filename>/index.html`.

Do you need a different path? Use `outputPath` key in the configuration of the file:

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

This is required e.g. for [Github Pages and 404 page](https://help.github.com/articles/creating-a-custom-404-page-for-your-github-pages-site/).
