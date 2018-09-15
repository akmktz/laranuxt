<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PostsSource
 * @package App
 *
 * @property int $id
 * @property string $type
 * @property int $user_id
 * @property string $name
 * @property string $account_name
 * @property bool $enabled
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
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
        'account_name' => 'string',
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'name', 'account_name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    //public static function getTableName()
    //{
    //    return with(new static)->getTable();
    //}

}
