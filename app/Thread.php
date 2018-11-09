<?php

namespace App;

use App\Http\Controllers\Helpers\QueryFilters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use App\Events\ModelActivityEvent;
use App\Http\Controllers\Helpers\Filterable;
use Illuminate\Support\Facades\DB;

class Thread extends Model
{

    use Filterable;

    protected $fillable = ['user_id', 'channel_id', 'title', 'title_slug', 'body'];

    protected static function boot()
    {

        parent::boot();

        static::addGlobalScope('repliesCount', function ($builder) {
            $builder->withCount('replies'); // access it like this: $thread->replies_count
        });

        static::created(function ($thread) {
            event(new ModelActivityEvent($thread, 'created'));
        });

        static::deleting(function ($thread) {

            // Deletion of this thread also causes the database to delete
            // its associated replies using a database cascade delete constraint
            // 
            // Get array of ids that will be used to delete the activities records
            // which refer to the replies of this deleted thread.
            // The likes activities on those replies will be deleted by design.
            $replies_ids = array_values(Reply::where('thread_id', $thread->id)->get(['id'])->toArray());

            // Do the deletion of the activities records
            // which refer to the replies on this deleted thread
            // and also the activities of the likes on those replies
            Activity::whereIn('subject_id', $replies_ids)
                ->where('subject_type', 'App\\Reply')
                ->where(function ($query) {
                    $query->where('activity_type', 'created')
                        ->orWhere('activity_type', 'liked');
                })->delete();

            // Delete the likeables table records of the likes that belong to the deleted replies
            DB::table('likeables')->whereIn('likeable_id', $replies_ids)
                ->where('likeable_type', 'App\\Reply')
                ->delete();

            // Delete the activity record that refers to this deleted thread
            $thread->activity()->delete();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function activity()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function subscribers()
    {
        return $this->hasMany(Subscription::class);
    }

    public function path()
    {
        return route('threads.show', [
            'channel' => $this->channel->slug,
            'thread' => $this->title_slug
        ]);
    }

    public function addReply($replyBody)
    {
        return $this->replies()->create([
            'user_id' => auth()->id(),
            'body' => $replyBody
        ]);
    }

    public function createdAtForHumans()
    {
        return $this->created_at->diffForHumans();
    }

    public function getRouteKeyName()
    {
        return 'title_slug';
    }

    public function setTitleSlugAttribute($title)
    {
        $title_slug = str_slug($title);

        if (static::where('title_slug', $title_slug)->exists()) {
            do {
                $title_slug = "{$title_slug}-" . time() . strtolower(str_random(5));
            } while (static::where('title_slug', $title_slug)->exists());
        }
        $this->attributes['title_slug'] = $title_slug;
    }

}
