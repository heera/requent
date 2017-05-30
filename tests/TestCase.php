<?php

namespace Requent;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Requent\Models\User;
use Requent\Models\Post;
use PHPUnit\Framework\TestCase as PhpunitTestCase;
use Faker\Factory as Faker;
use Requent\UrlParser\QueryStringParser as Parser;

class TestCase extends PhpunitTestCase
{
    public function setUp()
    {
        $this->config = require(__DIR__.'/../src/Requent/Config/requent.php');
        $this->bootEloquent();
        $this->migrateDatabase();
    }

    private function bootEloquent()
    {
        $this->capsule = new Capsule;
        $this->capsule->addConnection(array(
            'driver'  => 'sqlite',
            'database'  => ':memory:',
        ));
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
    }

    private function migrateDatabase()
    {
        $schema = $this->capsule->schema();
        $schema->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamps();
        });

        $schema->create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });

        $this->seedData();
    }

    private function seedData()
    {
        $faker = Faker::create();
        foreach (range(1, 10) as $item) {
            User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail
            ]);
        }

        foreach (range(1, 10) as $item) {
            $userId = array_rand([1,2,3]);
            $product = Post::create([
                'title' => $faker->sentence(3),
                'body' => $faker->paragraph(4),
                'user_id' => $userId+1,
            ]);
        }
    }

    protected function makeRequentInstance($query)
    {
        $key = $this->config['fields_parameter_name'];
        $parsedArray = Parser::parse(
            isset($query[$key]) ? $query[$key] : '', $key
        );
        return new Requent($this->config, $query, $parsedArray);
    }
}
