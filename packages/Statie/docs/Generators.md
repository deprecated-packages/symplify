## Generators

Posts? Lectures? Places?

All those items with **multiple records and own page** can be configured in `statie.yml`.

This is how default configuration for *posts* looks like:

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

            # which layout to use, this will be expanded to "_layouts/post.latte"
            layout: 'post'

            # and url prefix, e.g. file to path "_posts/2017-31-12-some-post.md" => "blog/2017/31/12/some-post"
            route_prefix: 'blog/:year/:month/:date'

            # optional parameters

            # an object that will wrap it's logic, you can add helper methods into it and use it in templates
            # Symplify\Statie\Renderable\File\File is used by default
            object: 'Symplify\Statie\Renderable\File\PostFile'
```


### How to Add New Generator?

1. Create directory in `/source`

```bash
/source/_letures
```

2. Create own layout in `/source/_layouts`

```bash
/source/_layouts/lecture.latte
```

3. Add configuration to `statie.yml`

```yml
# statie.yml
parameters:
    generators:
        lectures:
            variable: 'lecture'
            variable_global: 'lectures'
            path: '_lectures'
            layout: 'lecture'
            route_prefix: 'learn'
```


**Optional**

4. If you need own object with super method, create it:

```php
namespace MyWebsite\Statie;

use DateTime;
use Symplify\Statie\Renderable\File\AbstractFile;

final class LectureFile extends AbstractFile
{
    /**
     * Will it happen in the future => true
     * or already in past => false
     */
    public function isActive(): bool
    {
        // date is key that should be in every lecture file, e.g. "source/_lectures/doctrine-orm.md"
        if (! isset($this->configuration['date'])) {
            return false:
        }

        return $this->configuration['date'] >= new DateTime;
    }
}
```

5.  And configure it in `statie.yml`

```yml
# statie.yml
parameters:
    generators:
        lectures:
            ...
            object: 'MyWebsite\Statie\LectureFile'
```
