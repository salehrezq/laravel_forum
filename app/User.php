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
        'name', 'email', 'password', 'avatar_path'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getRouteKeyName() {
        return 'username';
    }

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

    /**
     * Creates a formatted key to be used in the cache
     * 
     * user.user_id.visited.thread_id
     * 
     * Example: user.7.visited.35
     * 
     * @param integer $threadId
     * @return string
     */
    public function visitedThreadCacheKey($threadId) {
        return sprintf("user.%s.visited.%s", auth()->id(), $threadId);
    }

    /**
     * Stores the timestamp in the cache; The timestamp of
     * when the user has recently visited the thread of $threadId
     * 
     * @param integer $threadId
     */
    public function readThread($threadId) {

        $key = $this->visitedThreadCacheKey($threadId);
        cache()->forever($key, \Carbon\Carbon::now());
    }

    /**
     * Checks if user has seen latest updates of a thread
     * 
     * What considered as updated thread is any of these four things:
     * 
     * - Updating any of the thread attributes.
     * - Updating a reply that belongs to the thread.
     * - Adding new reply to the thread.
     * - Deleting a reply from the thread
     * 
     * @param Thread|Model $thread
     * @return boolean
     */
    public function hasNotSeenLatestUpdatesFor($thread) {

        if (auth()->guest()) {
            return false;
        }

        $key = $this->visitedThreadCacheKey($thread->id);

        $latestReadTimestamp = cache($key);

        if ($latestReadTimestamp === null) {
            return false;
        }

        return $thread->updated_at > $latestReadTimestamp;
    }

    /**
     * Get the latest reply of this user.
     * 
     * @return Reply|Model
     */
    public function latestReply() {

        return Reply::where('user_id', $this->id)
                        ->orderBy('created_at', 'desc')
                        ->first();
    }

}
