# Default Autoconfigure

[![Build Status](https://img.shields.io/travis/Symplify/DefaultAutoconfigure/master.svg?style=flat-square)](https://travis-ci.org/Symplify/DefaultAutoconfigure)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/DefaultAutoconfigure.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/DefaultAutoconfigure)
[![Downloads](https://img.shields.io/packagist/dt/symplify/default-autoconfigure.svg?style=flat-square)](https://packagist.org/packages/symplify/default-autoconfigure)

**This bundle turns on autoconfigure for you!**

It turns this:

```yaml
# app/config/config.yml
services:
    _defaults:
        autoconfigure: true

    AppBundle\Security\PostVoter: ~
```

Into this:

```yaml
# app/config/config.yml
services:
    AppBundle\Security\PostVoter: ~
```

# Install

```bash
composer require symplify/default-autoconfigure
```

Add bundle to `AppKernel.php`:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symplify\DefaultAutoconfigure\SymplifyDefaultAutoconfigureBundle,
            // ...
        ];
    }
}
```


And that's it!


## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
