## Related Items

*Note: this applied only to Generated Elements.*

Do you write a post series? Help a reader and **show related posts bellow**.


### How to Setup?

**1. Add Post ids to `related_items` section in the Post**

```yaml
# _posts/2017-12-31-happy-new-year.md
---
id: 1
title: My first post
related_items: [2]
```


```yaml
---
id: 2
title: My second post
related_items: [1]
---
```

**2. Add Section to Post template:**

```twig
# _layout/post.latte
{var $relatedPosts = ($post|relatedPosts)}

<div n:if="count($relatedPosts)">
    <strong>Continue Reading</strong>
    <ul>
        {foreach $relatedPosts as $relatedPost}
            <li>
                <a href="/{$relatedPost['relativeUrl']}">{$relatedPost['title']}</a>
            </li>
        {/foreach}
    </ul>
</div>
```
