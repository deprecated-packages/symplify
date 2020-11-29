# Symfony Static Dumper

[![Downloads total](https://img.shields.io/packagist/dt/symplify/symfony-static-dumper.svg?style=flat-square)](https://packagist.org/packages/symplify/symfony-static-dumper/stats)

Dump your Symfony app to HTML + CSS + JS only static website.
Useful for deploy to Github Pages and other non-PHP static website hostings.

## Install

```bash
composer require symplify/symfony-static-dumper
```

Add to `config/bundles.php` if you're not using Flex:

```php
return [
    Symplify\SymfonyStaticDumper\SymfonyStaticDumperBundle::class => [
        'all' => true,
    ],
];
```

## Controller with Argument

To make Controller with argument, eg: `/blog/{slug}`, statically dumped, you have to implements `Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface` and implements 3 methods:
 - `getControllerClass()`
 - `getControllerMethod()`
 - `getArguments()`

For example, with the following provider:

```php
namespace TomasVotruba\SymfonyStaticDump\ControllerWithDataProvider;

use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use TomasVotruba\Blog\Controller\PostController;
use TomasVotruba\Blog\Repository\PostRepository;

final class PostControllerWithDataProvider implements ControllerWithDataProviderInterface
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getControllerClass(): string
    {
        return PostController::class;
    }

    public function getControllerMethod(): string
    {
        return '__invoke';
    }

    /**
     * @return string[]
     */
    public function getArguments(): array
    {
        $slugs = [];

        foreach ($this->postRepository->getPosts() as $post) {
            $slugs[] = $post->getSlug();
        }

        return $slugs;
    }
}
```

For the following controller:

```php
namespace TomasVotruba\Blog\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Blog\ValueObject\Post;

final class PostController extends AbstractController
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route(path="/blog/{slug}", name="post_detail", requirements={"slug"="\d+\/\d+.+"})
     */
    public function __invoke(string $slug): Response
    {
        $post = $this->postRepository->getBySlug($slug);

        return $this->render('blog/post.twig', [
            'post' => $post,
            'title' => $post->getTitle(),
        ]);
    }
}
```

## Use

```bash
bin/console dump-static-site
```

The website will be generated to `/output` directory in your root project.

To see the website, just run local server:

```bash
php -S localhost:8001 -t output
```

And open [localhost:8001](http://localhost:8001/) in your browser.

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
