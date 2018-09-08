<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PostsSource
 * @package App
 *
 * @var int $id
 * @var string $type
 * @var int $user_id
 * @var string $name
 * @var string $external_name
 * @var bool $enabled
 * @var \Carbon\Carbon $created_at
 * @var \Carbon\Carbon $updated_at
 */
class PostsSource extends Model
{
    const SOURCE_TYPE_TWITTER = 'TWITTER';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    public $casts = [
        'id' => 'integer',
        'type' => 'string',
        'user_id' => 'integer',
        'name' => 'string',
        'external_name' => 'string',
        'enabled' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'synchronized_at',
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

}
