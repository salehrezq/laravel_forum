<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model {

    protected $fillable = ['user_id', 'thread_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function thread() {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Tells whether this thread has already been subscribed to or not.
     * 
     * @param integer $threadId
     * @return boolean
     */
    public static function isSubscribedTo($threadId) {

        return static::where('user_id', auth()->id())->where('thread_id', $threadId)->exists();
    }

    /**
     * @param integer $threadId
     * @return boolean|string
     */
    public static function subscribeToggle($threadId) {

        if (!static::isSubscribedTo($threadId)) { // subscribe
            static::create([
                'user_id' => auth()->id(),
                'thread_id' => $threadId
            ]);
            return 'subscribe';
        } else { // unsubscribe
            $subscription = static::where('user_id', auth()->id())
                    ->where('thread_id', $threadId);

            if ($subscription !== null) {
                $subscription->delete();
                return 'unsubscribe';
            }
        }
        return false;
    }

}
