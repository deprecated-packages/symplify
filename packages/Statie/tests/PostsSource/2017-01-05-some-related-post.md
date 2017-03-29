---
layout: post
title: "Statie 4: How to Create The Simplest Blog"
perex: '''
Statie is very powerful tool for creating small sites. But you will use just small part of it's features, having just micro-sites. How to get to full 100%? <strong>Build a blog</strong>.
<br><br>
Today I will show you, <strong>how to put your first post</strong>.
'''
lang: en
---


## Create a Blog Page

This might be the simplest page to show all your posts:


```html
<!-- /source/blog.latte -->

---
layout: default
---

❴block content❵
<h2>Shouts Too Loud from My Hearth</h2>

❴foreach $posts as $post❵
<a href="/❴$post['relativeUrl']❵/">
    <h3>❴$post['title']❵</h3>
</a>
❴/foreach❵
❴/block❵
```

### You already see

- that all posts are in stored in `$posts` variable
- that every post has `relativeUrl`
- that every post should have a `title` (optional, but recommended)


## How Does it Work?

Statie will do 3 steps:

1. **Scans `/source/_posts` for any files**
- those files have to be in `YYYY-MM-DD-url-title.*` format
- that's how Statie can determine the date
2. **Converts Markdown and Latte syntax in them to HTML**
3. Stores them to `$posts` variable.


## How does a Post Content Look Like?

```html
<!-- source/_posts/2017-03-05-my-last-post.md -->

---
layout: "post"
title: "This my Last Post, Ever!"
---

This is my last post to all
```

### How to Show Post in Own Layout

As you can see, post has `layout: post`. It means it's displayed in `_layouts/post.latte`:

```twig
<!-- /source/_layouts/post.latte -->

❴extends "default"❵

❴block content_wrapper❵
<h2>❴$post['title']❵</h2>

❴$post['content']|noescape❵
❴/block❵
```

We have to also modify `default.latte, to include our post layout and replacte `❴block content}❴/block❵` with.

```twig
<!-- /source/_layouts/default.latte -->
...

❴block content_wrapper❵
❴block content}❴/block❵
❴/block❵

...
```

That should be it.

Save file, [look on the blog page](http://localhost:8000/blog) and see:

<div class="text-center">
    <img src="/../../../../assets/images/posts/2017/statie-4/statie-blog.png" class="thumbnail">
</div>

When you click a post title:

<div class="text-center">
    <img src="/../../../../assets/images/posts/2017/statie-4/statie-post.png" class="thumbnail">
</div>



### ProTip: Change Post Url

You see the url for the post is:

```
blog/2017/03/05/my-last-post/
```

or

```
blog/Year/Month/Day/FileSlug
```

This **can be changed by configuration**. Create `config.neon` and override default values:

```yaml
<!-- source/_config/config.neon -->

configuration:
postRoute: blog/:year/:month/:day/:title
```

Where `:year`, `:month`, `:day` and `:title` are all variables.

For example:

```yaml
configuration:
postRoute: my-blog/:year/:title
```

Would produce url:

```
my-blog/2017/my-last-post/
```

Got it? I know you do! **You are smart.**



In one of the next posts, I will show you some cool `PostFile` object features.


## Now You Know

- **That all posts are placed in `/source/_posts` directory and in `$posts` variable**.
- That post has to be in **named as `YYYY-MM-DD-title.md` format**
- That you can change the post generated url in `source/config/_config.neon` in `postRoute`.


Happy coding!