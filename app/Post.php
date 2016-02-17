<?php

namespace App;

use EloquentJs\Model\AcceptsEloquentJsQueries;
use EloquentJs\Model\EloquentJsQueries;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements AcceptsEloquentJsQueries
{
    use EloquentJsQueries;

    protected $guarded = [];
    protected $dates = ['published_at'];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
