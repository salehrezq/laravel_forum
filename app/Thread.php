<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use App\Events\ModelActivityEvent;
use App\Http\Controllers\Helpers\Filterable;
use Illuminate\Support\Facades\DB;

class Thread extends Model {

    use Filterable;

    protected $fillable = ['user_id', 'channel_id', 'title', 'body'];

    protected static function boot() {

        parent::boot();

        static::addGlobalScope('repliesCount', function ($builder) {
            $builder->withCount('replies'); // access it like this: $thread->replies_count
        });

        static::created(function($thread) {
            event(new ModelActivityEvent($thread, 'created'));
        });

        static::deleting(function ($thread) {

            // Get array of ids that will be used to delete the activities records
            // that belong to replies of this deleted thread.
            // It also includes the likes activities on those replies (by design)
            $replies_ids = Activity::select('replies.id')
                            ->join('replies', 'activities.subject_id', '=', 'replies.id')
                            ->where('activities.subject_type', 'App\\Reply')
                            ->where('replies.thread_id', $thread->id)->get()->toArray();

            // Do the deletion of activities records of replies and their associated likes of this deleted thread
            DB::table('activities')
                    ->whereIn('subject_id', array_values($replies_ids))
                    ->where('subject_type', 'App\\Reply')
                    ->delete();

            $thread->activity()->delete();
        });
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function channel() {
        return $this->belongsTo(Channel::class);
    }

    public function replies() {
        return $this->hasMany(Reply::class);
    }

    public function activity() {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function path() {
        return route('threads.show', [
            'channel' => $this->channel->slug,
            'thread' => $this->id
        ]);
    }

    public function addReply($replyBody) {
        $this->replies()->create([
            'user_id' => auth()->id(),
            'body' => $replyBody
        ]);
    }

    public function createdAtForHumans() {
        return $this->created_at->diffForHumans();
    }

}
