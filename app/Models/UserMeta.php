<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_meta';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'meta_key', 'meta_value'];

    /**
     * The meta_keys that can be assigned to users
     *
     * @var array
     */
    public $keys = ['profile_image'];

    /**
     * Meta Relationship to a user.
     *
     * @return Illuminate\Database\Eloquent\Model;
     */
    public function user()
    {
        return $this->hasOne('App\Models\User');
    }

    /**
     * Returns the vlaue of a meta key.
     *
     * @param  $value
     * @return $value|null
     */
    public function getValue($value)
    {
        $meta = $this->first()->pluck('meta_value', 'meta_key');

        if (isset($meta[$value])) {
            return $meta[$value];
        }

        return null;
    }
}
