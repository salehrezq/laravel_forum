<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function threads() {
        return $this->hasMany(Thread::class);
    }

    public function replies() {
        return $this->hasMany(Reply::class);
    }

    public function likedReplies() {
        return $this->morphedByMany(Reply::class, 'likeable');
    }

    public function likeReplyToggle($reply) {
        return $this->likedReplies()->toggle($reply);
    }

    public function activities() {
        return $this->hasMany(Activity::class);
    }

    public function subscribedThreads() {
        return $this->hasMany(Subscription::class);
    }

}
