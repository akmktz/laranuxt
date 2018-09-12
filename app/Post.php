<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Post
 * @package App
 *
 * @property  int $id
 * @property  string $type
 * @property  int $user_id
 * @property  int $source_id
 * @property  string $original_id
 * @property  \Carbon\Carbon $original_date
 * @property  string $title
 * @property  string $body
 * @property  string $url
 * @property  bool viewed
 * @property  \Carbon\Carbon $created_at
 * @property  \Carbon\Carbon $updated_at
 */
class Post extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    public $casts = [
        'id' => 'integer',
        'type' => 'string',
        'user_id' => 'integer',
        'source_id' => 'integer',
        'original_id' => 'string',
        'title' => 'string',
        'body' => 'string',
        'url' => 'string',
        'viewed' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'original_date',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function source()
    {
        return $this->belongsTo(PostsSource::class);
    }
}
