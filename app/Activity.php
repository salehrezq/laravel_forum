<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Activity extends Model {

    protected $fillable = ['user_id', 'activity_type'];

    public function subject() {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public static function feed($user) {

        $activities = static::select('activities.id', 'activities.subject_type', 'activities.activity_type', 'activities.created_at as subject_created_at', 'u.name', 't.id as threadId', DB::raw("CONCAT(SUBSTR(t.title, 1, 40), IF(LENGTH(t.title)>40,'...','')) AS threadTitle"), DB::raw("CONCAT(SUBSTR(t.body, 1, 200), IF(LENGTH(t.body)>200,'...','')) AS threadBody"), DB::raw("CONCAT(SUBSTR(r.body, 1, 200), IF(LENGTH(r.body)>200,'...','')) AS replyBody"), 'c.slug as channelSlug', DB::raw("CONCAT(LEFT((SELECT threads.title FROM threads WHERE (threads.id = r.thread_id)), 40), IF(LENGTH((SELECT threads.title FROM threads WHERE (threads.id = r.thread_id)))>40,'...','')) AS thread_title_for_reply"), DB::raw("(SELECT threads.id  FROM threads WHERE (threads.id = r.thread_id)) AS thread_id_for_reply"), DB::raw("(SELECT channels.slug FROM threads inner join channels ON threads.channel_id = channels.id WHERE threads.id = r.thread_id) AS thread_channel_slug_for_reply"))
                ->leftJoin('threads as t', 'activities.subject_id', '=', 't.id')
                ->leftJoin('replies as r', 'activities.subject_id', '=', 'r.id')
                ->leftJoin('users as u', 'activities.user_id', '=', 'u.id')
                ->leftJoin('channels as c', 'c.id', '=', 't.channel_id')
                ->where('activities.user_id', $user->id)
                ->orderBy('activities.created_at', 'DESC')
                ->get();

        // Group each set of activities that belong to date-of-day under a key of that date,
        // where the value of that key is an array of activity objects,
        $activities = $activities->groupBy(function ($activity) {
            $unix_timestamp = strtotime($activity->subject_created_at);
            return strftime("%Y-%d-%m", $unix_timestamp);
        });

        // I don't know how does this work.
        // It sorts the keys of $activities (from groupBy() above) in descent order
        // It uses the key value which is a string of date.
        return $activities->sortBy(function ($key) {
                    return $key;
                });
    }

}
