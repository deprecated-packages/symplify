## Push Content To Github Pages With Travis

The best way to use Statie is have [website on Github repository](https://github.com/TomasVotruba/tomasvotruba.cz), use Github Pages and use Travis to update generated content for you.

### How to Setup?

**1. Setup GH_TOKEN to `travis.yml`**

Add Github Token, so Travis is allowed to push to your Github repository.

- On Github go to *Settings* → *[Developer Settings](https://github.com/settings/developers)* → *[Personal Access Tokens](https://github.com/settings/tokens)* → *Generate New Token* - Select "Repo" scope

- Download [Travis CLI tool](https://github.com/travis-ci/travis.rb#installation)

- Run it in shell in root of your repository, where `<code>` is Github Token from step above:

    ```yaml
    travis encrypt GH_TOKEN=<code>
    ```

- It may happen that repository is not recognized by Travis. To fix that, go to `https://travis-ci.org/<repository-slug>`
    and add it there (like you do when you add new repository to be CI tested by Travis).

- When successful, this should encrypt your token to something like `f34vQ...<hundreds-of-chards>...Pa=`

- Finally, add this hash to `travis.yml`:

    ```yml
    env:
        global:
            - secure: f34vQ...Pa=
    ```

Now the Travis is able to push to your Github repository for you!


**2. And push command to `travis.yml`**

```yaml
# travis.yml
script:
    # this is needed to generate /output firstrepository_slug
    - vendor/bin/statie generate source
    # this works with content from /output
    - vendor/bin/statie push-to-github tomasvotruba/tomasvotruba.cz --token=${GH_TOKEN}
```

**But this will push content on every opened PR, even if not merged. That's not what you want, is it?**

So to push content only on changes accepted to `master` branch, just add:

```yaml
# travis.yml
script:
    - |
      if [[ "$TRAVIS_BRANCH" == "master" && "$TRAVIS_PULL_REQUEST" == "false" ]]; then
          vendor/bin/statie push-to-github tomasvotruba/tomasvotruba.cz --token=${GH_TOKEN}
      fi
```

That's better!