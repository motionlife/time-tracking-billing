<?php

use Illuminate\Database\Seeder;
use newlifecfo\Models\Arrangement;
use newlifecfo\Models\Client;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Engagement;
use newlifecfo\Models\Expense;
use newlifecfo\Models\Hour;
use newlifecfo\Models\Receipt;
use newlifecfo\Models\Templates\Position;
use newlifecfo\Models\Templates\Task;
use newlifecfo\Models\Templates\Taskgroup;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (($handle = fopen(__DIR__ . '\data\Billing\Billing2017-11-13.csv', "r")) !== FALSE) {
            $client_name = '';
            $eng_name = '';
            $position = '';
            $arr = null;
            fgetcsv($handle, 0, ",");//move the cursor one step because of header
            while (($line = fgetcsv($handle, 0, ",")) !== FALSE) {
                $skip = false;
                foreach ($line as $j => $entry) {
                    if ($j > 5) continue;
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
                } else if ($line[2]) {
                    $position = $line[2];
                } else if ($line[3]) {
                    $con_name = $line[3];
                    //check if is an un-enrolled arrangement
                    $eng = Engagement::firstOrCreate(['client_id' => $this->get_client_id($client_name), 'name' => $eng_name],
                        ['leader_id' => $this->get_consultant_id('New Life'), 'start_date' => date("1989-06-30")]);
                    //fetch first or create
                    $arr = Arrangement::firstOrCreate(['engagement_id' => $eng->id, 'consultant_id' => $this->get_consultant_id($con_name)],
                        ['position_id' => $this->get_pos_id($position), 'billing_rate' => 0, 'firm_share' => 1.0]);
                } else if ($line[4]) {
                    if (count($line) > 22 && $this->number($line[22])) {
                        $exp = Expense::create([
                            'arrangement_id' => $arr->id,
                            'report_date' => $line[4],
                            'hotel' => $this->number($line[12]),
                            'flight' => $this->number($line[13]),
                            'car_rental' => $this->number($line[14]),
                            'meal' => $this->number($line[15]),
                            'office_supply' => $this->number($line[16]),
                            'mileage_cost' => $this->number($line[18]),
                            'other' => $this->number($line[19]),
                            'description' => $line[21]]);
                        if ($line[20]) {
                            Receipt::Create(['expense_id' => $exp->id, 'filename' => $line[20]]);
                        }
                    } else {
                        $bh = $this->number($line[7]);
                        $nbh = $this->number($line[8]);
                        if ($bh || $nbh) {
                            Hour::Create([
                                'arrangement_id' => $arr->id,
                                'task_id' => $this->get_task_id($line[5], $line[6]),
                                'report_date' => $line[4],
                                'billable_hours' => $bh,
                                'non_billable_hours' => $nbh,
                                'description' => $line[10]
                            ]);
                            $arr->update(['billing_rate' => $this->number($line[9])]);
                        }
                    }
                }
            }
        }
    }

    public function get_task_id($group, $desc)
    {
        $g = Taskgroup::firstOrCreate(['name' => $group]);
        return Task::firstOrCreate(['taskgroup_id' => $g->id], ['description' => $desc])->id;
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

    public function get_pos_id($pos)
    {
        return Position::firstOrCreate(['name' => $pos])->id;
    }

    public function number($str)
    {
        return (float)filter_var($str, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
}
