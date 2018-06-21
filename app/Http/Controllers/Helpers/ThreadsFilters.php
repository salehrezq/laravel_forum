<?php

namespace App\Http\Controllers\Helpers;

class ThreadsFilters extends QueryFilters {

    public function user_id($user_id) {
        return $this->builder->where('user_id', $user_id)->latest();
    }

}
