# Requent [![Build Status](https://travis-ci.org/heera/requent.svg?branch=master)](https://travis-ci.org/heera/requent) [![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/heera/requent/master/LICENSE)

An elegant, light-weight GQL (Graph Query Language) like interface for Eloquent with zero configuration. It maps a request to eloquent query and transforms the result based on query parameters. It also supports to transform the query result explicitly using user defined transformers which provides a more secured way to exchange data from a public API with minimul effort.

## Installation

Add the following line in your "composer.json" file within "require" section and run `composer install` from terminal:

    "sheikhheera/requent": "1.0.*"

Once the installation is finished then you can start using it without any configuration. To use it, we need some models. So, imagine we've a User model, Post model and a Comment model. In our User model, we've a `hasMany`relation for "posts" and in Post model, we've a `hasMany` relation for "comments". So, if we've a route to fetch all users with their respective posts and comments of each post then we may do it using something like the following in a controller.

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;

class UserController extends Controller
{
    public function index($id = null)
    {
	return User::with('posts.comments')->get();
    }
}
```

The given code will return us all the user with their related posts and comments of each post. Now, using the package we can achieve the same thing but also we can do much more. So let's see a very basic example first. The following example is the most basic usage:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Requent\Requent;
use App\User;

class UserController extends Controller
{
    public function fetch($id = null)
    {
	return app(Requent::class)->resource(User::class)->fetch($id);
    }
}
```
