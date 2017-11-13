<?php

use Illuminate\Database\Seeder;
use newlifecfo\Models\Templates\State;

class US_StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new State(['code' => 'AL', 'name' => 'Alabama']))->save();
        (new State(['code' => 'AK', 'name' => 'Alaska']))->save();
        (new State(['code' => 'AS', 'name' => 'American Samoa']))->save();
        (new State(['code' => 'AZ', 'name' => 'Arizona']))->save();
        (new State(['code' => 'AR', 'name' => 'Arkansas']))->save();
        (new State(['code' => 'CA', 'name' => 'California']))->save();
        (new State(['code' => 'CO', 'name' => 'Colorado']))->save();
        (new State(['code' => 'CT', 'name' => 'Connecticut']))->save();
        (new State(['code' => 'DE', 'name' => 'Delaware']))->save();
        (new State(['code' => 'DC', 'name' => 'District of Columbia']))->save();
        (new State(['code' => 'FM', 'name' => 'Federated States of Micronesia']))->save();
        (new State(['code' => 'FL', 'name' => 'Florida']))->save();
        (new State(['code' => 'GA', 'name' => 'Georgia']))->save();
        (new State(['code' => 'GU', 'name' => 'Guam']))->save();
        (new State(['code' => 'HI', 'name' => 'Hawaii']))->save();
        (new State(['code' => 'ID', 'name' => 'Idaho']))->save();
        (new State(['code' => 'IL', 'name' => 'Illinois']))->save();
        (new State(['code' => 'IN', 'name' => 'Indiana']))->save();
        (new State(['code' => 'IA', 'name' => 'Iowa']))->save();
        (new State(['code' => 'KS', 'name' => 'Kansas']))->save();
        (new State(['code' => 'KY', 'name' => 'Kentucky']))->save();
        (new State(['code' => 'LA', 'name' => 'Louisiana']))->save();
        (new State(['code' => 'ME', 'name' => 'Maine']))->save();
        (new State(['code' => 'MH', 'name' => 'Marshall Islands']))->save();
        (new State(['code' => 'MD', 'name' => 'Maryland']))->save();
        (new State(['code' => 'MA', 'name' => 'Massachusetts']))->save();
        (new State(['code' => 'MI', 'name' => 'Michigan']))->save();
        (new State(['code' => 'MN', 'name' => 'Minnesota']))->save();
        (new State(['code' => 'MS', 'name' => 'Mississippi']))->save();
        (new State(['code' => 'MO', 'name' => 'Missouri']))->save();
        (new State(['code' => 'MT', 'name' => 'Montana']))->save();
        (new State(['code' => 'NE', 'name' => 'Nebraska']))->save();
        (new State(['code' => 'NV', 'name' => 'Nevada']))->save();
        (new State(['code' => 'NH', 'name' => 'New Hampshire']))->save();
        (new State(['code' => 'NJ', 'name' => 'New Jersey']))->save();
        (new State(['code' => 'NM', 'name' => 'New Mexico']))->save();
        (new State(['code' => 'NY', 'name' => 'New York']))->save();
        (new State(['code' => 'NC', 'name' => 'North Carolina']))->save();
        (new State(['code' => 'ND', 'name' => 'North Dakota']))->save();
        (new State(['code' => 'MP', 'name' => 'Northern Mariana Islands']))->save();
        (new State(['code' => 'OH', 'name' => 'Ohio']))->save();
        (new State(['code' => 'OK', 'name' => 'Oklahoma']))->save();
        (new State(['code' => 'OR', 'name' => 'Oregon']))->save();
        (new State(['code' => 'PW', 'name' => 'Palau']))->save();
        (new State(['code' => 'PA', 'name' => 'Pennsylvania']))->save();
        (new State(['code' => 'PR', 'name' => 'Puerto Rico']))->save();
        (new State(['code' => 'RI', 'name' => 'Rhode Island']))->save();
        (new State(['code' => 'SC', 'name' => 'South Carolina']))->save();
        (new State(['code' => 'SD', 'name' => 'South Dakota']))->save();
        (new State(['code' => 'TN', 'name' => 'Tennessee']))->save();
        (new State(['code' => 'TX', 'name' => 'Texas']))->save();
        (new State(['code' => 'UT', 'name' => 'Utah']))->save();
        (new State(['code' => 'VT', 'name' => 'Vermont']))->save();
        (new State(['code' => 'VI', 'name' => 'Virgin Islands']))->save();
        (new State(['code' => 'VA', 'name' => 'Virginia']))->save();
        (new State(['code' => 'WA', 'name' => 'Washington']))->save();
        (new State(['code' => 'WV', 'name' => 'West Virginia']))->save();
        (new State(['code' => 'WI', 'name' => 'Wisconsin']))->save();
        (new State(['code' => 'WY', 'name' => 'Wyoming']))->save();
    }
}
