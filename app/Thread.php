<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Reply;
use App\User;

class Thread extends Model {

    protected $fillable = ['user_id', 'title', 'body'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function replies() {
        return $this->hasMany(Reply::class);
    }

    public function path() {
        return route('threads.show', ['thread' => $this->id]);
    }

    public function addReply($replyBody) {
        $this->replies()->create([
            'user_id' => auth()->id(),
            'body' => $replyBody
        ]);
    }

    public function addThread($threadTitle, $threadBody) {
        return $this->create([
                    'user_id' => auth()->id(),
                    'title' => $threadTitle,
                    'body' => $threadBody
        ]);
    }

}
