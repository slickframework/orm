# Slick Object-relational mapping (ORM) package

[![Latest Version](https://img.shields.io/github/release/slickframework/orm.svg?style=flat-square)](https://github.com/slickframework/orm/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/slickframework/orm/master.svg?style=flat-square)](https://travis-ci.org/slickframework/orm)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/slickframework/orm/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/slickframework/orm/code-structure?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/slickframework/orm/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/slickframework/orm?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/slick/orm.svg?style=flat-square)](https://packagist.org/packages/slick/orm)


`Slick\Orm` is a simple, lightweight, ORM (object-relational mapping) library that
can help you with the task of mapping you domain entities with your database.
It uses simple annotations on entity classes to determine how data is mapped
when you select and/or update it. It also supports relations between entities.

This package is compliant with PSR-2 code standards and PSR-4 autoload standards.
It also applies the semantic version 2.0.0 specification.

## Install

Via Composer

``` bash
$ composer require slick/orm
```

## Usage

### Create an entity;

Entities are classes that most of the time had only properties and annotations
and you need to define them before you can actually use them.

So lets create a simple `Post` entity:

```php
namespace Domain;

use Slick\Orm\Annotations as Orm;
use Slick\Orm\Entity;
use Slick\Orm\EntityInterface;

class Post extends Entity
{
    /**
     * @readwrite
     * @Orm\Column type=integer, primaryKey, autoIncrement
     * @var integer
     */
    protected $id;

    /**
     * @readwrite
     * @Orm\Column type=text
     * @var string
     */
    protected $title;

    /**
     * @readwrite
     * @Orm\Column type=text
     * @var string
     */
    protected $body;
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($entityId)
    {
        $this->id = $entityId;
        return $this;
    }
}
```

Now with our entity defined lets create a blog post:

```php
use Domain\Post;

$post = new Post(['title' => 'My blog post title', 'body' => 'This is really long...']);
$post->save();  // Persists data in database
```

### Defining columns

As you can see the `Post` class has a special annotation `@Orm\Column` that marks properties
as a table columns in the `posts` database table.

`Slick\Orm` uses _convention over configuration_ to determine all the necessary settings to
map an entity to its persistence system.

Tables names uses the entity class name in plural and camel cased for word concatenation. For
example entity class `Person` results in a `people` table name and entity class `BlogPost`
will result in a `blogPosts` table name. You can change the mapped table name using 
the `@tableName` annotation in the class comment block.




## Testing

``` bash
$ vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email silvam.filipe@gmail.com instead of using the issue tracker.

## Credits

- [Slick framework](https://github.com/slickframework)
- [All Contributors](https://github.com/slickframework/common/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
