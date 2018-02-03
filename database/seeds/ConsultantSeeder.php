<?php

use Illuminate\Database\Seeder;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Templates\Contact;
use newlifecfo\User;

class ConsultantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = require __DIR__ . '/data/consultants.php';
        $faker = Faker\Factory::create('en_US');
        foreach ($data as $consultant) {
            $user = User::create(['first_name' => explode(" ", $consultant['name'])[0],
                'last_name' => isset(explode(" ", $consultant['name'])[1]) ? explode(" ", $consultant['name'])[1] : explode(" ", $consultant['name'])[0],
                'email' => $consultant['email'],
                'password' => bcrypt($consultant['password']),
                'role' => array_search('Consultant', User::ROLES),
                'priority' => $consultant['name'] == 'Burt Copeland' ? 51 : 3,
                'remember_token' => str_random(20)]);
            $contact = Contact::create(['email' => $user->email,
                'profile_img_path' => str_random(10) . '.jpg',
                'phone' => $consultant['phone'],
                'address_line1' => $faker->streetAddress,
                'city' => $faker->city,
                'state_id' => random_int(1, 59),
                'postcode' => $faker->postcode
            ]);
            Consultant::create([
                'user_id' => $user->id,
                'contact_id' => $contact->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'standard_rate' => strpos($consultant['rate'], '/') ? substr($consultant['rate'], 0, -1) : 0,
                'standard_percentage' => strpos($consultant['rate'], '%') ? substr($consultant['rate'], 0, -1) / 100 : 0,
                'isEmployee' => $consultant['isEmployee'] == 'Yes' ? '1' : '0',
                'inactive' => $consultant['inactive'] == 'Yes' ? '1' : '0'
            ]);
        }
    }
}
