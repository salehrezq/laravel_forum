<?php

namespace App\Http\Controllers\Helpers;

trait Filterable {

    public function scopeFilter($query, QueryFilters $filters) {
        return $filters->apply($query);
    }

}
