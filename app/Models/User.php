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
        'total_score',
        'reviews'
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
        'state',
        'profile_image'
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
     * Reviews Attribute.
     *
     * @return Eloquent
     */
    public function getReviewsAttribute()
    {
        return $this->reviews()->get();
    }

    /**
     * Reviews Attribute.
     *
     * @return Eloquent
     */
    public function getTotalScoreAttribute()
    {
        $score = $this->reviews()->avg('score');

        return round($score, 1);
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

    /**
     * Relationship with a review.
     *
     * @return Eloquent
     */
    public function reviews()
    {
        return $this->hasMany(
            'App\Models\Review',
            'recipient_id',
            'id'
        );
    }
}
