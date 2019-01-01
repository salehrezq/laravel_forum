<?php

use Illuminate\Database\Seeder;

class DBS_Channels extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        factory('App\Channel')->create([
            'name' => 'Clothes',
            'slug' => 'clothes'
        ]);

        factory('App\Channel')->create([
            'name' => 'Arts',
            'slug' => 'arts'
        ]);

        factory('App\Channel')->create([
            'name' => 'Sports',
            'slug' => 'sports'
        ]);

        factory('App\Channel')->create([
            'name' => 'Business',
            'slug' => 'business'
        ]);
        factory('App\Channel')->create([
            'name' => 'Networking',
            'slug' => 'networking'
        ]);

        factory('App\Channel')->create([
            'name' => 'Travel',
            'slug' => 'travel'
        ]);
        factory('App\Channel')->create([
            'name' => 'Food',
            'slug' => 'food'
        ]);

        factory('App\Channel')->create([
            'name' => 'Pets',
            'slug' => 'pets'
        ]);
        factory('App\Channel')->create([
            'name' => 'Shopping',
            'slug' => 'shopping'
        ]);

        factory('App\Channel')->create([
            'name' => 'Internet',
            'slug' => 'internet'
        ]);
    }

}
