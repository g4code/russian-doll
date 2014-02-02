russian-doll
======

> russian-doll - caching schema [php](http://php.net) library - inspired by [rails/cache_digests](https://github.com/rails/cache_digests)

## Install

Install through  [composer](https://getcomposer.org/) package manager.
Find it on [packagist](https://packagist.org/packages/g4/russian-doll).

    require: "g4/russian-doll": "*"
    
Dependency:
* [g4/mcache](https://github.com/g4code/mcache) package.

## Usage

Check mcache docs for details - [mcache](https://github.com/g4code/mcache/blob/master/README.md)

```php
<?php

$mcache = \G4\Mcache\McacheFactory::createInstance($driverName, $options, $prefix);

$key = \G4\RussianDoll\Key('posts');
$key
    ->addVariablePart($perPage)
    ->addVariablePart($page);

$russianDoll = new \G4\RussianDoll\RussianDoll($mcache);
$russianDoll->setKey($key);

// get data from cache
$posts = $russianDoll->fetch();

// write data to cache
$russianDoll->write($posts);

// invalidate cache entry
$russianDoll->->expire();
```


## Development

### Install dependencies

    $ make install

### Run tests

    $ make test

## License

(The MIT License)
see LICENSE file for details...
