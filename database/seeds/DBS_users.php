<?php

use Illuminate\Database\Seeder;

class DBS_users extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory('App\User', 20)->create();
    }
}
