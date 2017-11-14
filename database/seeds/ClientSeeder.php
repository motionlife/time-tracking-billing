<?php

use Illuminate\Database\Seeder;
use newlifecfo\Models\Client;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Outreferrer;
use newlifecfo\Models\Revenue;
use newlifecfo\Models\Templates\Contact;
use newlifecfo\Models\Templates\Industry;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create('en_US');
        $clients = array_map('str_getcsv',
            file(__DIR__.'\data\AllClients2017-11-13.csv',FILE_SKIP_EMPTY_LINES));
        $keys = array_shift($clients);
        foreach ($clients as $client) {
            $client = array_combine($keys, $client);
            $contact = Contact::create(['email' => $faker->unique()->safeEmail,
                'profile_img_path' => str_random(10) . '.jpg',
                'phone' => $faker->phoneNumber,
                'address_line1' => $faker->streetAddress,
                'city' => $faker->city,
                'state_id' => random_int(1, 59),
                'postcode' => $faker->postcode
            ]);
            $c = Client::create([
                'contact_id' => $contact->id,
                'industry_id' => Industry::where('name', $client['IndustryID'])->first()->id,
                'buz_dev_person_id' => $client['BusinessDevelopmentPerson'] == 'New Life CFO' ? '' :
                    Consultant::all()->first(function ($con, $key) use ($client) {
                        return $con->fullname() == $client['BusinessDevelopmentPerson'];
                    })->id,
                'outreferrer_id' => $client['OutsideReferrerSource'] == 'None' ? '' :
                    Outreferrer::all()->first(function ($out, $key) use ($client) {
                        return $out->fullname() == $client['OutsideReferrerSource'];
                    })->id,
                'name' => $client['ClientName'],
                'complex_structure' => $client['ComplexStructure'] == 'Yes',
                'messy_accounting_at_begin' => $client['MessyAccountingAtBegin'] == 'Yes',
            ]);
            if ($client['Year2015Revenues'] || $client['Year2015EBIT']) {
                Revenue::create([
                    'client_id' => $c->id,
                    'year' => 2015,
                    'revenue' => (float)filter_var($client['Year2015Revenues'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                    'ebit' =>(float)filter_var($client['Year2015EBIT'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                ]);
            }
            if ($client['Year2016Revenues'] || $client['Year2016EBIT']) {
                Revenue::create([
                    'client_id' => $c->id,
                    'year' => 2016,
                    'revenue' => (float)filter_var($client['Year2016Revenues'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                    'ebit' => (float)filter_var($client['Year2016EBIT'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                ]);
            }
        }

    }
}
