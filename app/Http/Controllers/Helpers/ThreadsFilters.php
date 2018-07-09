<?php

namespace App\Http\Controllers\Helpers;

class ThreadsFilters extends QueryFilters {

    public $queryfilters = ['user_id', 'popular', 'unanswered'];

    public function user_id($user_id) {
        return $this->builder->where('user_id', $user_id)->latest();
    }

    public function popular() {
        return $this->builder->orderBy('replies_count', 'DESC')->latest();
    }

    public function unanswered() {
        return $this->builder->whereRaw('id NOT IN (SELECT DISTINCT thread_id FROM replies)');
    }

}
