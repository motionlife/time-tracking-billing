<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use newlifecfo\Mail\NewSystemReady;
use newlifecfo\Models\Arrangement;
use newlifecfo\Models\Client;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Engagement;
use newlifecfo\Models\Receipt;
use newlifecfo\Models\Templates\Position;
use newlifecfo\Models\Templates\Task;
use newlifecfo\Models\Templates\Taskgroup;
use newlifecfo\Notifications\ApplicationPassed;
use newlifecfo\Notifications\ConfirmReports;
use newlifecfo\Notifications\LaunchApp;

class TestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verifiedConsultant');
    }

    public function index(Request $request)
    {
        //correct receipt file name
//        foreach (Receipt::all() as $item) {
//            $item->filename = 'receipts/'.$item->filename;
//            $item->save();
//        }
        if ($request->get('token') == 'haoxiong') {
//        return Excel::create('TestExcelFile', function ($excel) {
//            $excel->setTitle('Payroll Overview')
//                ->setCreator('Hao Xiong')
//                ->setCompany('New Life CFO')
//                ->setDescription('Your Payroll under the specified condition');
//
//            $excel->sheet('simple-sheet', function ($sheet) {
//
//                $sheet->fromArray(array(
//                    array('data1', 'data2'),
//                    array('data3', 'data4')
//                ));
//
//            });
//        })->export('xlsx');
            // return $this->numberTest($request);

            //Consultant::where('last_name','Xiong')->first()->user->notify(new ApplicationPassed(Consultant::where('last_name','Xiong')->first()->user));
            //Mail::to(Consultant::where('last_name','Xiong')->first()->user)->send(new NewSystemReady());
            $consultant = Consultant::where('last_name', 'Xiong')->first();
            $consultant->user->notify(new ConfirmReports($consultant->user));
            if ($request->get('launch') == 'true') {
                if ('code' == 'xh123456') {
                    Consultant::recognized()->each(function ($consultant) {
                        $user = $consultant->user;
                        $user->notify(new LaunchApp($user));
                    });
                } else {
                    Consultant::recognized()->each(function ($consultant) {
                        if (($consultant->first_name == 'Hao' && $consultant->last_name == 'Xiong')
                            || ($consultant->first_name == 'John' && $consultant->last_name == 'Doe')) {
                            $user = $consultant->user;
                            $user->notify(new LaunchApp($user));
                        }
                    });
                }
            }

        }
        return 'success';
    }


    private function numberTest($request)
    {

        if ($request->get('verify')) {
            $out = [];
            foreach (Consultant::recognized() as $con) {
                $temp = $this->verify($con->fullname());
                if ($temp)
                    array_push($out, [$con->fullname() => $temp]);
            }

            return json_encode($out);
        }

        $consultants = Consultant::recognized();
        $output = [];
        //verify hourly payroll and expense payroll
        foreach ($consultants as $consultant) {
            $totalpay = 0;
            $totalbillablehour = 0;
            $totalnonbillablehour = 0;
            $totalexpense = 0;
            foreach ($consultant->hours as $hour) {
                $totalpay += $hour->earned();
                $totalbillablehour += $hour->billable_hours;
                $totalnonbillablehour += $hour->non_billable_hours;
            }
            foreach ($consultant->expenses as $expense) {
                $totalexpense += $expense->total();
            }

            array_push($output, ['name' => $consultant->fullname(),
                'totalbh' => $totalbillablehour,
                'totalnbh' => $totalnonbillablehour,
                'totalpay' => $totalpay,
                'totalexpense' => $totalexpense,
            ]);
        }

        //verify billing and buz_dev payroll
        $clients = Client::all();
        $bills = [];
        foreach ($clients as $client) {
            $hoursbill = 0;
            $expensesbill = 0;
            foreach ($client->engagements as $eage) {
                foreach ($eage->arrangements as $arr) {
                    foreach ($arr->hours as $hour) {
                        $hoursbill += $hour->billable_hours * $arr->billing_rate;
                    }
                    foreach ($arr->expenses as $expense) {
                        $expensesbill += $expense->total();
                    }
                }
            }
            array_push($bills, ['hoursBill' => $hoursbill, 'expensesBill' => $expensesbill]);
        }

        return view('test', ['consultants' => $consultants,
            'result' => $output, 'csv' => $this->getTotalFromCSV(), 'clients' => $clients,
            'bills' => $bills
        ]);
    }

    private function getTotalFromCSV()
    {
        $out = [];
        if (($handle = fopen(__DIR__ . '\..\..\..\database\seeds\data\payroll\payroll_hours.csv', "r")) !== FALSE) {
            while (($line = fgetcsv($handle, 0, ",")) !== FALSE) {
                if (str_contains($line[0], 'Total')) {
                    $out[preg_replace('/\W\w+\s*(\W*)$/', '$1', $line[0])] = $this->number($line[12]);
                }
            }
        }
        return $out;
    }

    private function verify($consultant)
    {
        $log = [];
        if (($handle = fopen(__DIR__ . '\..\..\..\database\seeds\data\payroll\payroll_hours.csv', "r")) !== FALSE) {
            $client_name = '';
            $con_name = '';
            $eng_name = '';
            $group = '';
            $con_id = 0;
            $client_id = 0;
            $eng = null;
            $arr = null;
            $in = false;
            fgetcsv($handle, 0, ",");//move the cursor one step because of header
            $row = 1;
            while (($line = fgetcsv($handle, 0, ",")) !== FALSE) {
                $row++;
                $skip = false;
                foreach ($line as $j => $entry) {
                    if ($j > 4) continue;
                    if (stripos($entry, 'Total')) {
                        if ($in && str_contains($entry, $consultant)) $in = false;
                        $skip = true;
                        break;
                    }
                }
                if ($skip) continue;

                if ($line[0]) {
                    $con_name = $line[0];
                    if ($con_name == $consultant) $in = true;
                } else if ($line[1] && $in) {
                    $client_name = $line[1];
                } else if ($line[2] && $in) {
                    $eng_name = $line[2];
                    $con_id = $this->get_consultant_id($con_name);
                    $client_id = $this->get_client_id($client_name);
                    $eng = Engagement::where(['client_id' => $client_id, 'name' => $eng_name])->first();
                    if (!$eng) {
                        array_push($log, ['w-row$' => $row,
                            'client' => $client_name . '(' . $client_id . ')',
                            'consultant' => $con_name . '(' . $con_id . ')']);
                    }
                } else if ($line[3] && $in) {
                    $arr = Arrangement::where(['engagement_id' => $eng->id,
                        'consultant_id' => $con_id,
                        'position_id' => $this->get_pos_id($line[3])])->first();
                    //what if we can't find the arrangement
                    if (!$arr) {
                        array_push($log, ['engagement' => $eng_name . '(' . $eng->id . ')',
                            'client' => $client_name . '(' . $client_id . ')',
                            'consultant' => $con_name . '(' . $con_id . ')']);
                    }
                } else if ($line[5] && $in) {
                    $bh = $this->number($line[7]);
                    $nbh = $this->number($line[8]);
                    if ($bh || $nbh)
                        if (!$arr->hours->where('report_date', date('Y-m-d', strtotime($line[5])))
                            ->where('billable_hours', $bh)
                            ->where('non_billable_hours', $nbh)
                            ->first()) {
                            array_push($log, ['r#' => $row, 'bh' => $line[7], 'nbh' => $line[8], 'task' => $line[6]]);
                        }
                }
            }
            fclose($handle);
        }
        return $log;
    }

    public function get_task_id($group, $desc)
    {
        if ($group == '' || $group == ' ' || str_contains($group, 'blank')) {
            $group = 'Common';
        }
        if ($desc == '' || $desc == ' ' || str_contains($desc, 'blank')) {
            $desc = 'Other';
        }
        $g = Taskgroup::firstOrCreate(['name' => preg_replace('/\s+/', ' ', $group)]);
        return Task::firstOrCreate(['taskgroup_id' => $g->id, 'description' => preg_replace('/\s+/', ' ', $desc)])->id;
    }

    public function get_client_id($name)
    {
        return Client::where('name', $name)->first()->id;
    }

    public function get_consultant_id($name)
    {
        return Consultant::recognized()->first(function ($con) use ($name) {
            return $con->fullname() == $name;
        })->id;
    }

    public function get_pos_id($pos)
    {
        if ($pos == '' || $pos == ' ' || str_contains($pos, 'blank')) {
            $pos = 'Other';
        }
        return Position::firstOrCreate(['name' => $pos])->id;
    }

    public function number($str)
    {
        return (float)filter_var($str, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
}
