<?php

namespace App\Listeners;

use App\Events\UserCreatedEvent;
use App\Mail\ConfirmationEmail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserCreatedListener
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
     * @param  UserCreatedEvent $event
     * @return void
     */
    public function handle(UserCreatedEvent $event)
    {
        $user = $event->user;

        \Mail::to($user)->send(new ConfirmationEmail($user));
    }
}
