# Requent [![Build Status](https://travis-ci.org/heera/requent.svg?branch=master)](https://travis-ci.org/heera/requent) [![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/heera/requent/master/LICENSE)

An elegant, light-weight GQL (Graph Query Language) like interface for Eloquent with zero configuration. It maps a request to eloquent query and transforms the result based on query parameters. It also supports to transform the query result explicitly using user defined transformers which provides a more secured way to exchange data from a public API with minimal effort.

1. [Installation](#installation)
2. [How It Works](#how-it-works)
3. [Basic Example](#basic-example)
4. [Resource](#resource)
5. [Methods](#methods)
6. [Resource Key By](#key-by)
7. [Data Filtering (Transformers)](#transformer)

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
Now, we can make a request using: `http://example.com/users?fields=email,posts{title,comments{body}}`. This will give us the expected result, which would be an array of users (only email property) with all the related posts (only title property) and all the comments of each post (only body propert).

If we want to load any resource with relations without selecting any properties then we can just do it using the following request: `http://example.com/users?fields=posts{comments}`. This was the most basic example but let's explore it more.

## <a name="resource"> Resource
Actually, a resource is just an eloquent model, the first method we should call on the `Requent` class is `resource` which sets the primary resource we want to query on. So we can set the resource using couple of ways, for example:

```php
Requent::resource(User::class)
```

Also, we can use an object, i.e:

```php
Requent::resource(new User)
```

We can also pass a `Query Builder` for example:

```php
Requent::resource(app(User::class)->where('role', 'admin'))
```

So, we can call any scope methods as well, which just returns a `Query Builder` instance. The `resource` method returns the `Requent` object so we can chain methods, for example, we can call any query executing method (including other available methods of `Requent`).

## <a name="methods"> Methods

#### Get

We've seen `get` method earlier which just returns an array of users which is:

```php
return Requent::resource(User::class)->get();
```

#### Paginated Result

At this point, we'll get an array but we can retrieve paginated result using same `get` method and in this case we, we only need to provide a query string parameter in our `URL`: `http://example.com/users?fields=posts{comments}&paginate`, that's it. Also, we can set the paginator, for example: `http://example.com/users?fields=posts{comments}&paginate=simple`, this will return the paginated result using Laravel's `SimplePaginator` but by default it'll use `LengthAwarePaginator`.

#### Per Page

We can also tell how many pages we want to get for per page and it's just another parameter, for example: `http://example.com/users?fields=posts{comments}&paginate=simple&per_page=5`. If we provide `per_page=n` then we don't need to provide `&paginate` parameter unless we want to use the simple paginator instead of `default`. We can also customize these parameters, we'll check later on.

#### Paginate
Also, we can call the `paginate` method on the `Requent` directly, for example:

```php
return Requent::resource(User::class)->paginate(); // or paginate(10)
```


#### Simple Paginate

The `simplePaginate` will return paginated result using `Simple Paginator` [Check Laravel Pagination for More].(https://laravel.com/docs/5.4/pagination)

```php
return Requent::resource(User::class)->simplePaginate(); // or simplePaginate(10)
```

#### Find

If we want to retrieve a single `user` then we can use `find` and `first` method, for example:

```php
Requent::resource(User::class)->find($id);
```

#### First

For the first item we can call the `first` method:

```php
Requent::resource(User::class)->first();
```

#### Fetch

These are all the available methods for executing query but there is one more method which is `fetch`. This method can return any kind of result, an collection (array), paginated result or a singlr resource. Let's see an example:

```php

// In Controller

public function fetch($id = null)
{
    return Requent::resource(User::class)->fetch($id);
}
```
To use this method we need a route like: `Route::get('users/{id?}', 'UserController@fetch')` and then we can use this single route to get all kind of results, for example:

#### Get a collection of users (Array)

```php
http://example.com/users?fields=posts{comments}`
```

#### Get paginated result

```php
http://example.com/users?fields=posts{comments}&paginate=simple&per_page=5`
```

#### Get a single user (Array)

```php
http://example.com/users?fields=posts{comments}&paginate=simple&per_page=5`
```

This will be useful if we declare explicit route other than RESTfull routes for Resource Controllers [Check the Laravel documentation for more](https://laravel.com/docs/5.4/controllers#resource-controllers).

### <a name="key-by"> Resource Key By

The query results for a collection is simply an array with a zero based index but if we want then we can wrap our collection in a key using `keyBy` method, for example:

```php
return Requent::resource(User::class)->keyBy('users')->get();
```

This will return a collection of users (Array) as a key value pair where the key will be `users` and the result will be the valuse of that key. We can also use a key for a single user for example:

```php
return Requent::resource(User::class)->keyBy('user')->find(1);
```

In case of `fetch` we can use something like the following:

```php
public function fetch($id = null)
{
    return Requent::resource(User::class)->keyBy($id ? 'user' : 'users')->fetch($id);
}
```

The paginated result will remain the same, by default `Laravel` wraps the collection using the `data` as key.

## <a name="transformer"> Data Filtering (Transformers)

So far we've seen the default data transformation, which means that, a user can get any property or available relations of the resource just by asking it through the query string parameter `fields` (we can use something else other than `fields`), but there is no way to keep some data private if you are using this for a public `API`. Here, the `transformer` comes into play.

By default, the `Requent` uses a `DefaultTransformer` class to return only selected properties/relations, for example, if you send a request using a `URL` like following: `http://example.com/users?fields=email,posts{title,comments}}` then it'll return only selected properties/relations. In this case, it'll return what you ask for it but you may need to define explicitly what properties/relations a user can get from a request through query parameter. For this, you can create a custom transformer where you can tell what to return. To create a trunsformer, you just need to create transformer classes by extending the `Requent\Transformer\Transformer` class. For example:

```php
<?php

namespace App\Http\Transformers;

use Requent\Transformer\Transformer;

class UserTransformer extends Transformer
{
	public function transform($model)
	{
		// return $model;
		return [
			'id' => $model->id,
			'name' => $model->name,
			'email' => $model->email,
		];
	}
}
```
