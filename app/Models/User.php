<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Current authenticated user.
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    public $user;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'meta',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'username',
        'password',
        'first_name',
        'last_name',
        'city',
        'state'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Return the meta attribute as a key value array.
     *
     * @return array
     */
    public function getMetaAttribute()
    {
        $meta = $this->meta()->pluck('meta_value', 'meta_key');
        $meta = $this->metaImages($meta);

        return count($meta) ? $meta : null;
    }

    /**
     * Define relationship between user and user_meta.
     *
     * @return Illuminate\Database\Eloquent\Model;
     */
    public function meta()
    {
        return $this->hasMany('App\Models\UserMeta');
    }

    /**
     * Add CDN urls to profile and thumbnail image urls.
     */
    private function metaImages($meta)
    {
        if (isset($meta['profile_image'])) {
            $images = (array) json_decode($meta['profile_image']);

            foreach ($images as $size => $image) {
                $images[$size] = asset("storage/images/user/{$this->id}/{$size}/$image");
            }

            $meta['profile_image'] = $images;
        }

        return $meta;
    }

    /**
     * Set the user for the model
     */
    public function setUser($user = null)
    {
        if ($user) {
            $this->user = $user;
        } else {
            $this->user = \Auth::user() ?: \Auth::guard('api')->user();
        }

        return $this;
    }
}
