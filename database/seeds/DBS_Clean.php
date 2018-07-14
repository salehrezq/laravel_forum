<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DBS_Clean extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $this->clearDatabase();
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
