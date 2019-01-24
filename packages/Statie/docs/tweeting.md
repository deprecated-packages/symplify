# How to Tweet your Posts with Travis

## Enable in `statie.yaml` Config

```yaml
# statie.yaml
parameters:
    twitter_name: 'TomasVotruba'
    
    # how many days to wait before publishing another Tweet - set 0 days for testing
    twitter_minimal_gap_in_days: 1 
    # how old Tweets should be tweeted, to prevent 2-years old posting
    twitter_maximal_days_in_past: 60 
```

## Setup Twitter API 
 
1. Get Twitter Access Tokens

- Go to [apps.twitter.com/app/new](https://developer.twitter.com/app/new)
- Login under account you want to publish in and create new Application
- Then go to "Keys and Access Tokens"
- In the bottom click to "Create my access token"

2. Put them to `statie.yml.local` config:

```yaml
# statie.yml.local
parameters:
    twitter_consumer_key: 'TgnmCuTSH7gftcWOaFBUXPZzH'
    twitter_consumer_secret: '9oenODoyFsF2mG3zNevUY4HPwG76zGQBTBoWzfHUKCIorR2lJ0'
    twitter_oauth_access_token: '2463691352-mAMTJjo6kowEYddGTPpqdjUTWueQwWrLUdHpB9O'
    twitter_oauth_access_token_secret: 'ltb12xYHdWAHrtPWm5h31T6Rptfa1IyutensM5EsX47Dt'
```

**Never share them publicly**, if you don't want to have child porn tweets under your name. 

3. Add `statie.yml.local` to `statie.yml`

```diff
 # statie.yml
+imports:
+    - { resource: 'statie.yml.local', ignore_errors: true }
```

4. Add it to `.gitignore` so it's secret

```diff
+statie.yml.local
```

## Write a Tweet

Write "tweet" in your post.

```yaml
id: 252
title: 'How to Learn Playing Drums from 0'
tweet: 'New post on my blog: How to Learn Playing Drums from 0 #music'

# optional, relative path
tweet_image: '/assets/images/posts/drums.jpg'
---

It's a long journey...

```

And test it

```bsah
vendor/bin/statie tweet-post
```

Is it there? Good, it works and only few steps remain to fully automate this :)

## Setup Travis

Now we only put that logic on Travis and we're done.

7. Open Travis for your repository, e.g. [https://travis-ci.org/TomasVotruba/tomasvotruba.cz](https://travis-ci.org/TomasVotruba/tomasvotruba.cz)

8. Got to *More options* => *Settings*

9. In *Environment Variables* add 4 variables with they values. They are hidden by default, so don't worry:
    - `TWITTER_CONSUMER_KEY`
    - `TWITTER_CONSUMER_SECRET`
    - `TWITTER_OAUTH_ACCESS_TOKEN`
    - `TWITTER_OAUTH_ACCESS_TOKEN_SECRET`

10. Then setup cron, so posts are being published even if you don't write and have a break.

11. Go to *Cron Jobs* → `master` branch → *Daily* → *Always run* → Add

12. Let `.travis.yml` know, that he should publish it

```yaml
# .travis.yml
language: php

matrix:
    include:
        - php: 7.3
          env: TWEET=1

script:
    # tweets posts
    - if [[ $TRAVIS_BRANCH == "master" && $TRAVIS_PULL_REQUEST == "false" && $TWEET != "" ]]; then vendor/bin/publish-new-tweet; fi
```

13. Now you can [quit Twitter](https://www.tomasvotruba.cz/blog/2017/01/20/4-emotional-reasons-why-I-quit-my-twitter/) if you want and you posts will be still there :)
