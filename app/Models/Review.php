<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'accepted_at',
        'id',
        'type',
        'review',
        'recipient_id',
        'sender_id',
        'score'
    ];

    /**
     * Accessor for the recipient.
     *
     * @return User
     */
    public function getRecipientAttribute()
    {
        return $this->recipient()->first();
    }

    /**
     * Accessor for the sender.
     *
     * @return User
     */
    public function getSenderAttribute()
    {
        return $this->sender()->first();
    }

    /**
     * Relationship with the user receiving the review.
     *
     * @return Eloquent
     */
    public function recipient()
    {
        return $this->belongsTo('App\Models\User', 'recipient_id', 'id');
    }

    /**
     * Relationship with the User that sent the invite.
     *
     * @return Eloquent
     */
    public function sender()
    {
        return $this->belongsTo('App\Models\User', 'sender_id', 'id');
    }
}
