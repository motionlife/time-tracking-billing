<?php

use Illuminate\Database\Seeder;
use newlifecfo\Models\Arrangement;
use newlifecfo\Models\Client;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Engagement;
use newlifecfo\Models\Templates\Position;

class EngagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (($handle = fopen(__DIR__ . '\data\Engagement2017-11-13.csv', "r")) !== FALSE) {
            $client_name = '';
            $eng_name = '';
            $start = '';
            $share = 0;
            $eng_id = 0;
            fgetcsv($handle, 0, ",");//move the cursor one step because of header
            while (($line = fgetcsv($handle, 0, ",")) !== FALSE) {
                if ($line[0]) {
                    $client_name = $line[0];
                } else if ($line[2]) {
                    $eng_name = $line[2];
                } else if ($line[3]) {
                    $start = $line[3];
                } else if ($line[4]) {
                    $share = is_numeric($line[4]) ? $line[4] / 100 : 0;
                } else if ($line[5]) {
                    $eng_id = Engagement::create([
                        'client_id' => $this->get_client_id($client_name),
                        'leader_id' => $this->get_consultant_id($line[5]),
                        'name' => $eng_name,
                        'start_date' => $start,
                        'buz_dev_share' => $share,
                    ])->id;
                } else if ($line[6]) {
                    Arrangement::create([
                        'engagement_id' => $eng_id,
                        'position_id' => $this->get_position_id($line[6]),
                        'consultant_id' => $this->get_consultant_id($line[7]),
                        'billing_rate' => $this->number($line[9]),
                        'firm_share' => $this->number($line[10]) / 100,
                    ]);
                }
            }
            fclose($handle);
        }
    }

    public function get_client_id($name)
    {
        return Client::where('name', $name)->first()->id;
    }

    public function get_consultant_id($name)
    {
        return Consultant::all()->first(function ($con) use ($name) {
            return $con->fullname() == $name;
        })->id;
    }

    public function get_position_id($name)
    {
        return Position::where('name', $name)->first()->id;
    }
    public function number($str)
    {
        return (float)filter_var($str, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
}
