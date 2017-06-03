# Requent [![Build Status](https://travis-ci.org/heera/requent.svg?branch=master)](https://travis-ci.org/heera/requent) [![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/heera/requent/master/LICENSE)

An elegant, light-weight GQL (Graph Query Language) like interface for Eloquent with zero configuration. It maps a request to eloquent query and transforms the result based on query parameters. It also supports to transform the query result explicitly using user defined transformers which provides a more secured way to exchange data from a public API with minimul effort.

## Installation

Add the following line in your "composer.json" file within "require" section and run `composer install` from terminal:

    "sheikhheera/requent": "1.0.*"

This will install the package and once the installation is finished then you can start using it without any configurations but you can configure it for your need.

## Basic Usage:

To use the package in any of your controllers, you can import it using `use Requent\Requent` and can make an instance of it using `app(Requent::class)` from within any method but you can use the Requent facade class. To use the facade, you need to register the `ServiceProvider` class and the `Facade` class in your `config\app.php` file. To register the service provider add the following line in the `providers` array of your `config\app.php`:

    Requent\RequentServiceProvider::class,
    
Then, add the following line in the `aliases` section:

    'Requent' => Requent\Facade\Requent::class,
    
Now, you can use the package using the `Requent` facade from any controller, for example:

```php
<?php

namespace App\Http\Controllers;

use Requent;
use App\User;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return Requent::resource(User::class)->get();
    }
}
```
