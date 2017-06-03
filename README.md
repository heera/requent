# Requent [![Build Status](https://travis-ci.org/heera/requent.svg?branch=master)](https://travis-ci.org/heera/requent) [![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/heera/requent/master/LICENSE)

An elegant, light-weight GQL (Graph Query Language) like interface for Eloquent with zero configuration. It maps a request to eloquent query and transforms the result based on query parameters. It also supports to transform the query result explicitly using user defined transformers which provides a more secured way to exchange data from a public API with minimul effort.

1. [Installation](#Installation)

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

## The Use Case

So far, we just saw how to install and use it in our controller classes but we need to understand the ues case of the package first. So, the package actually allow us to read any resource from an end point (route) using GQL (Graph Query Language) like interface. For example, imagine that, we've a `User` model and a `Post` model and a `Comment` model. The `User` has many posts using `hasMany` relationship and each `Post` model have many comments using `hasMany` relationship. So, as usual, we can grab all the posts of a user with comments of each post simply using `User::with('posts.comments')->get()`. Nothing is new here, all the `laravel` things.

To, access any resource, we need a route, so for this example, we can declare a route using the following:
```php
Route::get('users', 'UserController@index');
````

Now, if we hit the route using `http:\\example.com\users`, then it'll retuen all the users with posts and comments of each post. Now, this package will allow us to do the same thing using a different approach. For example, if we make request to the same route using `http:\\example.com\users?fields=posts{comments}` then it'll retuen the expected result. In this case, our `UserController` will need the following code:

```php
public function index()
{
    return Requent::resource(User::class)->get();
}
```

This is the most basic use case but it offers more. We'll see everything step by step but first, let's get the basic overview of it. At this point, the `get` method will return all the users as collection but we can get a paginated result even without changing the code in our `index` method. For exampe, to get a paginated result using `laravel's` default paginator, we can make a request using `http:\\example.com\users?fields=posts{comments}&paginate`. This will return us a paginated result using the `LengthAwarePaginator` but if we want then we can change the paginator by giving the value to the `paginate` parameter using something like `&paginate=simple`. So, we'll get the paginated result using `SimplePaginator`.

We can also pass `per_page=10` to get 10 items for per request. In this case, if we pass the `per_page=10` parameter then we can skip the `paginate` parameter unless we want to set the paginator class from `default` to `simple`. So, we can get the paginated result using `http:\\example.com\users?fields=posts{comments}&per_page=10`. We can modify the parameter to indicate the number of pages for pagination in config including other settings. We'll walkthrough each options later.

At this point, we've already selected the relations but if we want then we can also chose the attributes of each resource from the `URL` for example, we can make a request to get only specific attributes from a model using something like the following: `http:\\example.com\users?fields=email,posts{title,comments}`. This will return us `email` of users and `title` of their related `posts` and all related comments of each post but with all properties.

We can also add some query constraints from the `URL` for example, if we want to order posts using 'title' property then we can do it using: `http:\\example.com\users?fields=posts.orderBy('title'){comments}`. So, this is the most basic overview of this package but it also supports custome transformers, which allow us to transform (filter) the query result explicitly where we can control which fields/properties or relations should be loaded by a request. This will allow us to use this package to build public `API`s with more control over our resources. Now, we'll see every available methods the `Requent` offers to interact with any model or every customization options we have.
