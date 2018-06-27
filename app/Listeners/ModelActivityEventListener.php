<?php

namespace App\Listeners;

use App\Events\ModelActivityEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Activity;

class ModelActivityEventListener {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ModelActivityEvent  $modelEvent
     * @return void
     */
    public function registerModelActivity(ModelActivityEvent $modelEvent) {

        $model = $modelEvent->model;
        $activity = $modelEvent->activity;

        $model->activity()->create([
            'user_id' => auth()->id(),
            'activity_type' => $activity
        ]);
    }

}
