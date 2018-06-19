<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Thread;

class Channel extends Model {

    public function threads() {
        return $this->hasMany(Thread::class);
    }

    /**
     * Override the search key for actions
     * that use model route binding from id to slug
     * @return string
     */
    public function getRouteKeyName() {
        return 'slug';
    }

}
