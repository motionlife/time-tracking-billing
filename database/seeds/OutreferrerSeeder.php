<?php

use Illuminate\Database\Seeder;
use newlifecfo\Models\Outreferrer;
use newlifecfo\Models\Templates\Contact;
use newlifecfo\User;

class OutreferrerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     */
    public function run()
    {
        $faker = Faker\Factory::create('en_US');
        $names = ['Michael Hall', 'Mark Connelly', 'Jason Kos', 'Jason Williford', 'Ken Stiles', 'Dennis Howard', 'David Boyette'];
        static $password;
        foreach ($names as $name) {
            $user = User::create(['first_name' => explode(" ",$name)[0],
                'last_name' => explode(" ",$name)[1],
                'email' => $faker->unique()->safeEmail,
                'password' => $password ?: $password = bcrypt('secret'),
                'role' => 3,
                'remember_token' => str_random(20)]);

            $contact = Contact::create(['email' => $user->email,
                'profile_img_path' => str_random(10).'.jpg',
                'phone' => $faker->phoneNumber,
                'address_line1' => $faker->streetAddress,
                'city' => $faker->city,
                'state_id' => random_int(1, 59),
                'postcode' => $faker->postcode
            ]);
            Outreferrer::create([
                'user_id' => $user->id,
                'contact_id' => $contact->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name
            ]);
        }

    }
}
