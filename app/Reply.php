<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Events\ModelActivityEvent;

class Reply extends Model {

    protected $fillable = ['body', 'user_id'];
    protected $with = ['user']; // eager load the related user of the reply.

    protected static function boot() {

        static::created(function($reply) {
            event(new ModelActivityEvent($reply, 'created'));
        });
    }

    /**
     * Check if the reply is liked by the authenticated user.
     * @return boolean
     */
    public function isAlreadyLiked() {
        return DB::table('likeables')->where('user_id', auth()->id())->where('likeable_id', $this->id)->exists();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function thread() {
        return $this->belongsTo(Thread::class);
    }

    public function activity() {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function usersLikes() {
        return $this->morphToMany(User::class, 'likeable');
    }

    public function likedReplyToggleByUser($user) {
        return $this->usersLikes()->toggle($user);
    }

    public function createdAtForHumans() {
        return $this->created_at->diffForHumans();
    }

}
