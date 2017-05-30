<?php

namespace Requent\Models;

use Requent\Models\Post;
use Requent\Models\Comment;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = ['id'];
    
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getNameAttribute($name)
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }
}
