<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Subscription;
use App\Thread;
use App\User;

class DBS extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $this->clearDatabase();

        $threads = $this->threads($count = 1, $user_id = 7);
        $this->authUser(1);  // user to subscribe to thread
        foreach ($threads as $key => $thread) {
            $this->subscribeTo($thread);
            $replies = $this->replies(3, $thread, $delay = 0);
        }

//        $threads = $this->threads($count = 1, $user_id = 9);
//        $this->authUser(2);  // user to subscribe to thread
//        foreach ($threads as $key => $thread) {
//            $this->subscribeTo($thread);
//            $replies = $this->replies(3, $thread, $delay = 0);
//        }
    }

    private function threads($count, $user_id) {
        $threads = [];
        for ($i = 0; $i < $count; $i++) {

            $date = Carbon\Carbon::now()->subDays(10);

            $threads[] = factory('App\Thread')->create([
                'user_id' => $user_id,
            ]);
        }
        return $threads;
    }

    public function replies($count, $thread, $delay = 0) {

        $replyCount = 0;

        for ($i = 0; $i < $count; $i++) {

            sleep($delay);

            $reply = $thread->replies()->save(factory('App\Reply')->create([
                        'thread_id' => $thread->id,
                        'user_id' => mt_rand(2, 20),
                        'body' => 'reply ' . ++$replyCount,
            ]));
            $this->notifySubscribers($reply);
        }
    }

    private function subscribeTo($thread) {
        Subscription::subscribeToggle($thread->id);
    }

    private function authUser($id) {
        auth()->loginUsingId($id);
    }

    private function notifySubscribers($reply) {

        $thread = Thread::find($reply->thread_id);

        if ($thread !== null) {

            $usersIds = $thread->subscribers->pluck('user_id')->toArray();

            $this->excludeReplyOwner($reply, $usersIds);

            if (count($usersIds) > 0) {
                $users = User::whereIn('id', $usersIds)->get();
                Notification::send($users, new \App\Notifications\ThreadNotification($reply->id, $reply->created_at));
            }
        }
    }

    private function excludeReplyOwner($reply, &$usersIds) {
        if (($key = array_search($reply->user_id, $usersIds)) !== false) {
            unset($usersIds[$key]);
        }
    }

    private function clearDatabase() {

        DB::table('subscriptions')->truncate();
        DB::table('notifications')->truncate();
        DB::table('activities')->truncate();
        DB::table('replies')->truncate();
        DB::table('likeables')->truncate();
        DB::table('threads')->delete();
        DB::update("ALTER TABLE threads AUTO_INCREMENT = 1");
    }

}
