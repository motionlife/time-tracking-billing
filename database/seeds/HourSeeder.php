<?php

use Illuminate\Database\Seeder;
use newlifecfo\Models\Arrangement;
use newlifecfo\Models\Client;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Engagement;
use newlifecfo\Models\Hour;
use newlifecfo\Models\Templates\Position;
use newlifecfo\Models\Templates\Task;
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
        dump($this->verify('Alex Liu'));
        return;
        if (($handle = fopen(__DIR__ . '\data\payroll\Payroll_Hours2017-11-13.csv', "r")) !== FALSE) {
            $client_name = '';
            $eng_name = '';
            $position = '';
            $con_name = '';
            $group = '';
            $arr = null;
            fgetcsv($handle, 0, ",");//move the cursor one step because of header
            while (($line = fgetcsv($handle, 0, ",")) !== FALSE) {
                $skip = false;
                foreach ($line as $j => $entry) {
                    if ($j > 4) continue;
                    if (stripos($entry, 'Total')) {
                        $skip = true;
                        break;
                    }
                }
                if ($skip) continue;

                if ($line[0]) {
                    $con_name = $line[0];
                } else if ($line[1]) {
                    $client_name = $line[1];
                } else if ($line[2]) {
                    $eng_name = $line[2];
                } else if ($line[3]) {
                    $position = $line[3];
                } else if ($line[4]) {
                    $group = $line[4];
                    //check if is an un-enrolled arrangement
                    $con_id = $this->get_consultant_id($con_name);
                    $eng = Engagement::firstOrCreate(['client_id' => $this->get_client_id($client_name), 'name' => $eng_name],
                        ['leader_id' => $this->get_consultant_id('New Life'), 'start_date' => date("0000-00-00")]);
                    //fetch first or create
                    $arr = Arrangement::firstOrCreate(['engagement_id' => $eng->id, 'consultant_id' => $con_id],
                        ['position_id' => $this->get_pos_id($position), 'billing_rate' => 0, 'firm_share' => 0]);
                } else if ($line[5]) {
                    $arr->update(['billing_rate' => $this->number($line[9]), 'firm_share' => $this->number($line[11]) / 100]);
                    Hour::firstOrCreate([
                        'arrangement_id' => $arr->id, 'report_date' => $line[5], 'task_id' => $this->get_task_id($group, $line[6])],
                        ['billable_hours' => $this->number($line[7]), 'non_billable_hours' => $this->number($line[8]),
                            'description' => $line[13],]);
                    //Payroll file doesn't contain expense information, so must use another seeder
                }
            }
            fclose($handle);
        }
    }

    private function verify($consultant)
    {
        $log = [];
        if (($handle = fopen(__DIR__ . '\data\payroll\Payroll_Hours2017-11-13.csv', "r")) !== FALSE) {
            $client_name = '';
            $eng_name = '';
            $con_id = 0;
            $client_id = 0;
            $row = 0;
            $eng = null;
            $arr = null;
            $in = false;
            fgetcsv($handle, 0, ",");//move the cursor one step because of header
            while (($line = fgetcsv($handle, 0, ",")) !== FALSE) {
                $row++;
                $skip = false;
                foreach ($line as $j => $entry) {
                    if ($j > 4) continue;
                    if (stripos($entry, 'Total')) {
                        if ($in && stripos($entry, $consultant)) $in = false;
                        $skip = true;
                        break;
                    }
                }
                if ($skip) continue;
                $con_name = $line[0];
                if ($con_name == $consultant || $in) {
                    $in = true;
                    if ($line[1]) {
                        $client_name = $line[1];
                    } else if ($line[2]) {
                        $eng_name = $line[2];
                        $con_id = $this->get_consultant_id($con_name);
                        $client_id = $this->get_client_id($client_name);
                        $eng = Engagement::where(['client_id' => $client_id, 'name' => $eng_name])->first();
                        if (!$eng) {
                            array_push($log, ['wrong-row$' => $row, 'client' => $client_name . '(' . $client_id . ')',
                                'consultant' => $con_name . '(' . $con_id . ')']);
                        }
                    } else if ($line[3]) {
                        $arr = Arrangement::where(['engagement_id' => $eng->id, 'consultant_id' => $con_id, 'position_id' => $this->get_pos_id($line[3])])->first();
                        //what if we can't find the arrangement
                        if (!$arr) {
                            array_push($log, ['engagement' => $eng_name . '(' . $eng->id . ')',
                                'client' => $client_name . '(' . $client_id . ')',
                                'consultant' => $con_name . '(' . $con_id . ')']);
                        }

                    } else if ($line[5]) {

                        if ($arr->billing_rate != $this->number($line[9]) ||
                            !$arr->hourReports->where('report_date', $line[5])
                                ->where('billable_hours', $this->number($line[7]))
                                ->where('non_billable_hours', $this->number($line[8]))
                                ->first()) {
                            array_push($log, ['report_date' => $line[5],
                                'row#' => $row]);
                        } else {
                            array_push($log, ["correct-row#" => $row, "correct-date" => $line[5]]);
                        }
                    }
                }
            }
            fclose($handle);
        }
        return $log;
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
