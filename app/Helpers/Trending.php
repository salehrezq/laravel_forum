<?php
/**
 * Created by PhpStorm.
 * User: Saleh
 * Date: 10/28/2018
 * Time: 6:16 AM
 */

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Thread;


class Trending
{
    public function incrementViews($threadId)
    {
        DB::table('threads')->where('id', $threadId)->increment('views');
    }

    public function getTrendingThreads()
    {
        return Thread::where('views', '>', 0)
            ->with('channel')
            ->orderBy('views', 'desc')
            ->take(5)
            ->get();
    }

}