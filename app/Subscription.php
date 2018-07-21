<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

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

    public static function notifySubscribers($reply) {

        $thread = Thread::find($reply->thread_id);

        if ($thread !== null) {

            $usersIds = $thread->subscribers->pluck('user_id')->toArray();

            static::excludeReplyOwner($reply, $usersIds);

            if (count($usersIds) > 0) {

                $users = User::whereIn('id', $usersIds)->get();
                Notification::send($users, new \App\Notifications\ThreadNotification($reply->id));
            }
        }
    }

    /**
     * Exclude the id of the reply owner so that the owner
     * will not receive a notification due to his own reply
     *
     * @param Reply $reply
     * @param array $usersIds
     */
    private static function excludeReplyOwner($reply, &$usersIds) {
        if (($key = array_search($reply->user_id, $usersIds)) !== false) {
            unset($usersIds[$key]);
        }
    }

}
