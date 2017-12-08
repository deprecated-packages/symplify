## Headline Anchors

Do you want to **share link to 3rd paragraph** of the post?

When your hover any headline, an anchor link to it will appear on the left. Just click & share it!

![Headline Anchors](github-like-headline-anchors.png)


*Note: this applied only to Generated Elements (e.g. posts etc.), not to standalone pages (e.g. index, contact).*


### How to Setup?

**1. Enable in `statie.yml`**

```yaml
# statie.yml
parameters:
    markdown_headline_anchors: true
```


**2. Add style to your css**

Feel free to modify this sample to your needs:

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


That's it!