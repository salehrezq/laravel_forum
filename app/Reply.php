<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Events\ModelActivityEvent;

class Reply extends Model {

    protected $fillable = ['body', 'user_id'];
    // When updating a reply or adding a new reply or deleting a reply
    // the updated_at attribute of the corresponding thread will be updated
    protected $touches = ['thread'];

    protected static function boot() {

        parent::boot();

        static::created(function($reply) {
            event(new ModelActivityEvent($reply, 'created'));
        });

        static::deleted(function ($reply) {

            // Do the deletion of the activity record that refers to this deleted reply
            // and also the activities of the likes on this reply
            Activity::where('subject_id', $reply->id)
                    ->where('subject_type', 'App\\Reply')
                    ->where(function ($query) {
                        $query->where('activity_type', 'created')
                        ->orWhere('activity_type', 'liked');
                    })->delete();

            DB::table('likeables')
                    ->where('likeable_id', $reply->id)
                    ->where('likeable_type', 'App\\Reply')
                    ->delete();
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
