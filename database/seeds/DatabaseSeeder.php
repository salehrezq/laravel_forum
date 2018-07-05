<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
 
        DB::table('activities')->truncate();
        DB::table('replies')->truncate();
        DB::table('likeables')->truncate();
        DB::table('threads')->delete();
        DB::update("ALTER TABLE threads AUTO_INCREMENT = 1");

        $user = auth()->loginUsingId(7);

       factory('App\Thread', 1)->create(['user_id' => $user->id])->each(function ($thread) {
           for ($i = 0; $i < 100; $i++) {
               $thread->replies()->save(factory('App\Reply')->create(['thread_id' => $thread->id]));
           }
       });
    }

}
