<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Post
 * @package App
 *
 * @var int $id
 * @var string $type
 * @var int $user_id
 * @var int $source_id
 * @var string $original_id
 * @var \Carbon\Carbon $original_date
 * @var string $title
 * @var string $body
 * @var string $url
 * @var bool $viewed
 * @var \Carbon\Carbon $created_at
 * @var \Carbon\Carbon $updated_at
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
        return $this->hasOne(PostsSource::class);
    }
}
