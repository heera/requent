<?php

namespace Requent;

use Requent\Requent;
use Requent\Models\User;
use Requent\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Requent\Transformer\UserTransformer;
use Illuminate\Database\Eloquent\Collection;


class RequentTest extends TestCase
{
    public function testBasic()
    {
        $queryString = [];
        $requent = $this->makeRequentInstance($queryString);
        
        $result = $requent->resource(User::class)->fetch();
        $this->assertTrue(!!count($result['data']));
    }

    public function testCollectionData()
    {
        $queryString = ["fields" => "posts"];
        $requent = $this->makeRequentInstance($queryString);
        
        $result = $requent->resource(User::class)->get();
        $this->assertTrue(!!count($result['data']));
    }

    public function testPaginatedData()
    {
        $queryString = ["fields" => "posts"];
        $requent = $this->makeRequentInstance($queryString);

        $result = $requent->resource(User::class)->paginate(2);
        $this->assertTrue(!!count($result['data']) && $result['per_page'] == 2);
    }

    public function testDefaultTransformedData()
    {
        $queryString = ["fields" => "name,posts{id,title}"];
        $requent = $this->makeRequentInstance($queryString);

        $result = $requent->resource(User::class)->first();
        $this->assertTrue(isset($result['name']) && (Bool) count($result['posts']));
    }

    public function testCustomTransformedData()
    {
        $queryString = ["fields" => "posts{id,title}"];
        $requent = $this->makeRequentInstance($queryString);

        $result = $requent->resource(User::class, UserTransformer::class)->first();
        $this->assertTrue(isset(
            $result['name'],
            $result['email'],
            $result['posts'][0]['title']
        ));
    }

    public function testQueryLimitedTransformedData()
    {
        $queryString = ["fields" => "posts.limit(2){id,title}"];
        $requent = $this->makeRequentInstance($queryString);

        $result = $requent->resource(User::class, UserTransformer::class)->first();
        $this->assertTrue(isset(
            $result['name'],
            $result['email']
        ) && (Bool) (count($result['posts']) == 2));
    }

    public function testKeyBy()
    {
        $queryString = [];
        $requent = $this->makeRequentInstance($queryString);
        
        $result = $requent->resource(User::class)->keyBy('users')->get();
        $this->assertTrue(!!count($result['users']));
    }

    public function testOriginalModel()
    {
        $queryString = [];
        $requent = $this->makeRequentInstance($queryString);
        
        $result = $requent->resource(User::class)->original()->first();
        $this->assertTrue($result instanceof Model);
    }

    public function testOriginalCollection()
    {
        $queryString = [];
        $requent = $this->makeRequentInstance($queryString);
        
        $result = $requent->resource(User::class)->original()->get();
        $this->assertTrue($result instanceof Collection);
    }

    public function testResourceCouldBeQueryBuilder()
    {
        $queryString = [];
        $requent = $this->makeRequentInstance($queryString);
        
        $result = $requent->resource(
            (new User)->orderByDesc('id')->limit(1)
        )->original()->get();
        $this->assertTrue(count($result) == 1);
    }
}
