<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Thread;

class Reply extends Model {

    protected $fillable = ['body', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function thread() {
        return $this->belongsTo(Thread::class);
    }

    public function createdAtForHumans() {
        return $this->created_at->diffForHumans();
    }

}
