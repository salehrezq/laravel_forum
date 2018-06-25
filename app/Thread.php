<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Reply;
use App\User;
use App\Channel;
use App\Http\Controllers\Helpers\Filterable;

class Thread extends Model {

    use Filterable;

    protected $with = ['channel'];

    protected $fillable = ['user_id', 'channel_id', 'title', 'body'];

    protected static function boot() {

        parent::boot();

        static::addGlobalScope('repliesCount', function ($builder) {
            $builder->withCount('replies'); // access it like this: $thread->replies_count
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
