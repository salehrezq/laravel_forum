<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Reply;
use App\User;

class Thread extends Model {

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function replies() {
        return $this->hasMany(Reply::class);
    }

    public function path() {
        return route('threads.show', ['thread' => $this->id]);
    }

    public function addReply($reply) {
        $this->replies()->create([
            'user_id' => auth()->id(),
            'body' => $reply
        ]);
    }

}
