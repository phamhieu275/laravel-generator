<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bar extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'bars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['name', 'content', 'publish_date', 'author_id', 'rate', 'score'];
}
