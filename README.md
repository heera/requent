# Requent [![Build Status](https://travis-ci.org/heera/requent.svg?branch=master)](https://travis-ci.org/heera/requent) [![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/heera/requent/master/LICENSE)

An elegant, light-weight GQL (Graph Query Language) like interface for Eloquent with zero configuration. It maps a request to eloquent query and transforms the result based on query parameters. It also supports to transform the query result explicitly using user defined transformers which provides a more secured way to exchange data from a public API with minimal effort.

1. [Installation](#installation)
2. [The Use Case](#how-it-works)
3. [Basic Example](#basic-example)


## <a name="installation">Installation

Add the following line in your "composer.json" file within "require" section and run `composer install` from terminal:

    "sheikhheera/requent": "1.0.*"

This will install the package and once the installation is finished then you can start using it without any configurations but you can configure it for your need.

## <a name="how-it-works">How It Works

This package will allow us to query resources through the request query string parameter. For example, if we've a `User` model and the `User` model has many posts (`Post` model) and each post has many comments (`Comment` model) then we can query the users with their posts and comments of each posts by sending a request like the followig: `http://example.com/users?fields=posts{comments}`. This is the most basic use case but it offers more. We can also select properties of each model through query string, for example, if we want to select only the emal field from the `User` model and title from `Post` and body from the `Comment` model then we can just do it by sending a request like the following: `http://example.com/users?fields=email,posts{title,comments{body}}`.

## <a name="basic-example">Basic Example

To use this package, we need to create some resources (Eloquent Models). For this demonstration, we'll use the same idea using User, Post and Comment models for an imaginary blog. The User model has a `hasMany` relation for posts and the Post model has a `hasMany` relation for comments. So, we need a route, which could be a resourceful route but we'll use an explicite route declaration here:

```php
Route::get('users', 'UserController@index');
```

Now, we need a controller which is just a simple controller, i.e:

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
Now, we can make a request using: `http://example.com/users?fields=email,posts{title,comments{body}}`. This will give us the expected result.
