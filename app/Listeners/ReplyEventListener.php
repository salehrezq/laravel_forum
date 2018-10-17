<?php

namespace App\Listeners;

use App\Events\ReplyEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\MentionUsers;

class ReplyEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ReplyEvent  $event
     * @return void
     */
    public function handle(ReplyEvent $event)
    {
        if($event->operation === 'store'){
            \App\Subscription::notifySubscribers($event->relatedThread, $event->reply);
            MentionUsers::mentionUsersIn($event->reply);
        }elseif ($event->operation === 'update'){
            MentionUsers::mentionUsersIn($event->reply);
        }
    }
}
