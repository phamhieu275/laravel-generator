<?php

namespace Test\Sample\Models;

use Illuminate\Database\Eloquent\Model;

class Foo extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'foos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [];
}
