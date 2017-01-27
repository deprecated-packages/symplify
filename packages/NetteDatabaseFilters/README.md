# Nette Database Filters

[![Build Status](https://img.shields.io/travis/Zenify/NetteDatabaseFilters.svg?style=flat-square)](https://travis-ci.org/Zenify/NetteDatabaseFilters)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Zenify/NetteDatabaseFilters.svg?style=flat-square)](https://scrutinizer-ci.com/g/Zenify/NetteDatabaseFilters)
[![Latest stable](https://img.shields.io/packagist/v/zenify/nette-database-filters.svg?style=flat-square)](https://packagist.org/packages/zenify/nette-database-filters)


Port of [Filters concept](https://github.com/Zenify/DoctrineFilters) to Nette\Database.


## Install

```sh
composer require zenify/nette-database-filters
```

And register extension:

```yaml
# app/config/config.neon
extensions:
	- Zenify\NetteDatabaseFilters\DI\NetteDatabaseFiltersExtension
```


## Usage

Let's create filter that **hides all deleted comments** = those where "is_deleted" column is `TRUE`.

Create class that implements [Zenify\NetteDatabaseFilters\Contract\FilterInterface](src/Contract/FilterInterface.php).

```php
namespace App\Database\Filter;

use Nette\Database\Table\Selection;
use Zenify\NetteDatabaseFilters\Contract\FilterInterface;


final class SoftdeletableFilter implements FilterInterface
{

    public function applyFilter(Selection $selection)
    {
        // 1. apply only to "comment" table
        $tableName = $selection->getName();
        if ($tableName !== 'comment') {
            return;
        }

        // 2. show only visible (not deleted) comments
        $selection->where('is_deleted = ?', FALSE);
    }

}
```

And register as a service:

```neon
services:
    - App\Database\Filter\SoftdeletableFilter
```

And that's it!


Hm, but we want to **keep them displayed for admin user**. How to do that?


```php
namespace App\Database\Filter;

use Nette\Database\Table\Selection;
use Nette\Security\User;
use Zenify\NetteDatabaseFilters\Contract\FilterInterface;


final class SoftdeletableFilter implements FilterInterface
{

    /**
     * @var User
     */
    private $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }


    public function applyFilter(Selection $selection)
    {
        // 1. skip filter for admin user
        if ($this->user->isLoggedIn() && $this->user->hasRole('admin')) {
            return;
        }

        // 2. apply only to "comment" table
        $tableName = $selection->getName();
        if ($tableName !== 'comment') {
            return;
        }

        // 3. show only visible (not deleted) comments
        $selection->where('is_deleted = ?', FALSE);
    }

}
```


Simple, right? :)

P.S.: Same can be done for Front/Admin presenters via `Nette\Application\Application` service.




## Testing

```sh
composer check-cs
vendor/bin/phpunit
```


## Contributing

Rules are simple:

- new feature needs tests
- all tests must pass
- 1 feature per PR

We would be happy to merge your feature then!
