<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Baz extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'bazs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['title', 'slug'];
}
