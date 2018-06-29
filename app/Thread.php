<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use App\Events\ModelActivityEvent;
use App\Http\Controllers\Helpers\Filterable;

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
