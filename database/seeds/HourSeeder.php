<?php

use Illuminate\Database\Seeder;
use newlifecfo\Models\Client;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Engagement;
use newlifecfo\Models\Expense;
use newlifecfo\Models\Hour;
use newlifecfo\Models\Receipt;
use newlifecfo\Models\Templates\Taskgroup;

class HourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $csv = array_map('str_getcsv',
            file('C:\Users\HaoXiong\PhpstormProjects\NewLifeCFO\database\seeds\data\billing\Billing2017-11-10.csv', FILE_SKIP_EMPTY_LINES));
        array_shift($csv);//shift off csv header
        $client_name = '';
        $eng_name = '';
        $con_name = '';
        foreach ($csv as $line) {
            $skip = false;
            foreach ($line as $i => $entry) {
                if ($i > 4) continue;
                if (stripos($entry, 'Total')) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) continue;

            if ($line[0]) {
                $client_name = $line[0];
            } else if ($line[1]) {
                $eng_name = $line[1];
            } else if ($line[3]) {
                $con_name = $line[3];
            } else if ($line[4]) {
                $argmnts = Engagement::all()->first(function ($eng) use ($client_name, $eng_name) {
                    return $eng->name == $eng_name && $eng->client_id == $this->get_client_id($client_name);
                })->arrangements;
//                    ->first(function ($argmnt) use ($con_name) {
//                    return $argmnt->consultant_id == $this->get_consultant_id($con_name);
//                })->id;
                foreach ($argmnts as  $a)
                {
                    echo $a;
                }
                dd([$client_name,$eng_name]);

                return;
                if (!$this->money($line[22])) {
                    Hour::create([
                        'arrangement_id' => $agmnt_id,
                        'task_id' => $this->get_task_id($line[5], $line[6]),
                        'report_date' => $line[4],
                        'billable_hours' => $line[7],
                        'non_billable_hours' => $line[8],
                        'description' => $line[10],

                    ]);
                } else {
                    $eid = Expense::create([
                        'arrangement_id' => $agmnt_id,
                        'report_date' => $line[4],
                        'hotel' => $this->money($line[12]),
                        'flight' => $this->money($line[13]),
                        'car_rental' => $this->money($line[14]),
                        'meal' => $this->money($line[15]),
                        'office_supply' => $this->money($line[16]),
                        'mileage_cost' => $this->money($line[18]),
                        'other' => $this->money($line[19]),
                        'description' => $line[21],
                    ])->id;
                    Receipt::create([
                        'expense_id' => $eid,
                        'filename' => $line[20],
                    ]);
                }
            }
        }
    }

    public function get_task_id($group, $desc)
    {
        return Taskgroup::where('name', $group)->first()
            ->tasks()->first(function ($t) use ($desc) {
                return $t->description == $desc;
            })->id;
    }

    public function get_client_id($name)
    {
        return Client::where('name', $name)->first()->id;
    }

    public function get_consultant_id($name)
    {
        return Consultant::all()->first(function ($con, $key) use ($name) {
            return $con->fullname() == $name;
        })->id;
    }

    public function money($str)
    {
        return (float)filter_var($str, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
}
