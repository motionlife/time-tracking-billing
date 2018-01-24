<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use newlifecfo\Models\Client;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Engagement;
use newlifecfo\Models\Expense;
use newlifecfo\Models\Hour;

class AccountingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verifiedConsultant');
    }

    public function index(Request $request, $isAdmin = false, $payroll = true)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $eid = explode(',', $request->get('eid'));
        $state = $request->get('state');
        $client_id = $request->get('cid');
        $file = $request->get('file');
        $user = Auth::user();
        if ($payroll) {
            $consultant = $isAdmin ? ($request->get('conid') ? Consultant::find($request->get('conid')) : null) : $user->consultant;
            if ($consultant) {
                $hourReports = Hour::reported($start, $end, $eid, $consultant, $state);
                $expenseReports = Expense::reported($start, $end, $eid, $consultant, $state);
                $pg_hours = $this->paginate($hourReports, $request->get('perpage') ?: 20, $request->get('tab') == 2 ?: $request->get('page'));
                $pg_expenses = $this->paginate($expenseReports, $request->get('perpage') ?: 20, $request->get('tab') != 2 ?: $request->get('page'));
                $income = [$hourReports->sum(function ($hour) {
                    return $hour->earned();
                }), $expenseReports->sum(function ($exp) {
                    return $exp->total();
                })];
                $buz_devs = $this->getBuzDev($consultant, $start, $end, $eid, $state);

                if ($file == 'excel') return $this->exportExcel(['hours' => $hourReports, 'expenses' => $expenseReports, 'buz_devs' => $buz_devs, 'income' => $income,
                    'filename' => $this->filename($consultant, $start, $end, $state, $eid, 'PAYROLL')]);

                return view('wage', ['clientIds' => Engagement::groupedByClient($consultant),
                        'hours' => $pg_hours, 'expenses' => $pg_expenses,
                        'income' => $income,
                        'buz_devs' => $buz_devs,
                        'admin' => $isAdmin,
                        'consultant' => $consultant]
                );

            } else {
                $incomes = [];
                $buz_dev_incomes = [];
                $hourNumbers = [];
                $consultants = Consultant::recognized();
                foreach ($consultants as $consultant) {
                    $id = $consultant->id;
                    $hourNumbers[$id] = [0, 0];
                    $incomes[$id] = $this->getIncome($consultant, $start, $end, $eid, $state, $hourNumbers[$id]);
                    $buz_dev_incomes[$id] = $this->getBuzDev($consultant, $start, $end, $eid, $state)['total'];
                }
                $sum = $this->sumIncome($incomes, $buz_dev_incomes);
                if ($request->session()->has('data') && $file == 'excel') {
                    $data = $request->session()->get('data');
                    return $this->exportExcel(array_add($data, 'filename', $this->filename(null, $start, $end, $state, $eid, 'PAYROLL')), true);
                } else {
                    $data = ['admin' => $isAdmin,
                        'consultants' => $consultants,
                        'incomes' => $incomes,
                        'buzIncomes' => $buz_dev_incomes,
                        'hrs' => $hourNumbers,
                        'income' => [$sum[0], $sum[1]],
                        'buz_devs' => ['total' => $sum[2]]];
                    $request->session()->put('data', $data);
                    return view('wage', array_add($data, 'clientIds', Engagement::groupedByClient(null)));
                }
            }
        } else {
            //client billing controller
            $client = Client::find($client_id);
            if ($client) {
                //bill for the specific client
                $hourBill = $client->hourBill($start, $end, $state, $eid);
                $expenseBill = $client->expenseBill($start, $end, $state, $eid);

                $pg_hours = $this->paginate($hourBill[1], $request->get('perpage') ?: 20, $request->get('tab') == 2 ?: $request->get('page'));
                $pg_expenses = $this->paginate($expenseBill[1], $request->get('perpage') ?: 20, $request->get('tab') != 2 ?: $request->get('page'));
                $bill = [$hourBill[0], $expenseBill[0]];

                if ($file == 'excel') return $this->exportExcel(['hours' => $hourBill[1], 'expenses' => $expenseBill[1], 'buz_devs' => null, 'bill' => $bill,
                    'filename' => $this->filename($client, $start, $end, $state, $eid, 'Bill')], false, true);

                return view('bill', ['clientIds' => $client->getEngagementIdName(),
                        'hours' => $pg_hours, 'expenses' => $pg_expenses,
                        'bill' => $bill,
                        'admin' => $isAdmin,
                        'client' => $client]
                );

            } else {
                //all client bill review
                $bills = [];
                $hourNumbers = [];
                $clients = Client::all();
                foreach ($clients as $client) {
                    $id = $client->id;
                    $hourNumbers[$id] = [0, 0];
                    $bills[$id] = $this->getBills($client, $start, $end, $eid, $state, $hourNumbers[$id]);
                }
                $sum = $this->sumIncome($bills, null);
                if ($request->session()->has('data') && $file == 'excel') {
                    $data = $request->session()->get('data');
                    return $this->exportExcel(array_add($data, 'filename', $this->filename(null, $start, $end, $state, $eid, 'Bill')), true, true);
                } else {
                    $data = ['admin' => $isAdmin,
                        'clients' => $clients,
                        'bills' => $bills,
                        'hrs' => $hourNumbers,
                        'bill' => [$sum[0], $sum[1]]];
                    $request->session()->put('data', $data);
                    return view('bill', array_add($data, 'clientIds', Engagement::groupedByClient(null)));
                }
            }
        }

    }

    private function getBills(Client $client, $start, $end, $eid, $state, &$hrs = null)
    {
        $hourBill = $client->hourBill($start, $end, $state, $eid);;
        $expenseBill = $client->expenseBill($start, $end, $state, $eid);
        $hrs[0] = $hourBill[2];
        $hrs[1] = $hourBill[3];
        return [$hourBill[0], $expenseBill[0]];
    }

    private function sumIncome($incomes, $buz_dev_incomes)
    {
        $sum_bh = 0;
        $sum_ex = 0;
        $sum_dev = 0;
        foreach ($incomes as $income) {
            $sum_bh += $income[0];
            $sum_ex += $income[1];
        }
        if ($buz_dev_incomes)
            foreach ($buz_dev_incomes as $dev) {
                $sum_dev += $dev;
            }
        return [$sum_bh, $sum_ex, $sum_dev];
    }

    private function getIncome(Consultant $consultant, $start, $end, $eid, $state, &$hrs = null)
    {
        $hourReports = Hour::reported($start, $end, $eid, $consultant, $state);
        $expenseReports = Expense::reported($start, $end, $eid, $consultant, $state);
        $sumHours = $hourReports->reduce(function ($carry, $hour) {
            return [$carry[0] + $hour->billable_hours, $carry[1] + $hour->non_billable_hours, $carry[2] + $hour->earned()];
        });
        $hrs[0] = $sumHours[0] ?: 0;
        $hrs[1] = $sumHours[1] ?: 0;
        return [$sumHours[2], $expenseReports->sum(function ($exp) {
            return $exp->total();
        })];
    }

    private function getBuzDev(Consultant $consultant, $start, $end, $eid, $state)
    {
        $total = 0;
        $engs = [];
        foreach ($consultant->dev_clients()->withTrashed()->get() as $dev_client) {
            foreach ($dev_client->engagements()->withTrashed()->get() as $engagement) {
                if (!$eid[0] || in_array($engagement->id, $eid)) {
                    if ($engagement->buz_dev_share == 0) continue;
                    $devs = $engagement->incomeForBuzDev($start, $end, $state);
                    if ($devs) {
                        $tbh = 0;
                        foreach ($engagement->arrangements()->withTrashed()->get() as $arrangement) {
                            foreach ($arrangement->hours as $hour) {
                                $tbh += $hour->billable_hours;
                            }
                        }
                        array_push($engs, [$engagement, $devs, $tbh]);
                        $total += $devs;
                    }
                }
            }
        }
        return ['total' => $total, 'engs' => $engs];
    }

    private function exportExcel($data, $all = false, $bill = false)
    {
        if ($bill) {
            $all ? Excel::create($data['filename'], function ($excel) use ($data) {
                $excel->setTitle('Billing Overview')
                    ->setCreator('Hao Xiong')
                    ->setCompany('New Life CFO')
                    ->setDescription('All the Billing under the specified condition(file name)');
                $excel->sheet('Client Bill', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Client', 'Billable Hours', 'Non-billable Hours', 'Engagement Bill($)', 'Expense Bill($)', 'Total($)'])
                        ->setAllBorders('thin')
                        ->cells('A1:F1', function ($cells) {
                            $cells->setBackground('#3bd3f9');
                            $cells->setFontFamily('Calibri');
                            $cells->setFontWeight('bold');
                            $cells->setAlignment('center');
                        });
                    $content = [];
                    foreach ($data['clients'] as $client) {
                        $cid = $client->id;
                        $salary = $data['bills'][$cid];
                        array_push($content, [$client->name, $data['hrs'][$cid][0], $data['hrs'][$cid][1], number_format($salary[0], 2), number_format($salary[1], 2), number_format($salary[0] + $salary[1], 2)]);
                    }
                    array_push($content, []);
                    array_push($content, ['Engagement Total($)', $data['bill'][0], 'Expense Total($)', $data['bill'][1]]);
                    $sheet->fromArray($content, null, "A2", true, false);
                });
            })->export('xlsx') : Excel::create($data['filename'], function ($excel) use ($data) {
                $excel->setTitle('Billing Overview')
                    ->setCreator('Hao Xiong')
                    ->setCompany('New Life CFO')
                    ->setDescription('Your Bill under the specified condition(file name)');

                $excel->sheet('Engagement Bill($' . number_format($data['bill'][0], 2) . ')', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Consultant', 'Engagement', 'Report Date', 'Position', 'Task', 'Billable Hours', 'Rate($)', 'Rate Type', 'Billed Type', 'Billed($)', 'Report Status'])
                        ->setAllBorders('thin')
                        ->cells('A1:K1', function ($cells) {
                            $cells->setBackground('#3bd3f9');
                            $cells->setFontFamily('Calibri');
                            $cells->setFontWeight('bold');
                        });
                    $content = [];
                    foreach ($data['hours'] as $i => $hour) {
                        $arr = $hour->arrangement;
                        $eng = $arr->engagement;
                        array_push($content, [$hour->consultant->fullname(), $eng->name, $hour->report_date,  $arr->position->name, $hour->task->description, number_format($hour->billable_hours, 2), $hour->rate, $hour->rate_type == 0 ? 'Billing rate' : 'Pay rate', $eng->paying_cycle == 0 ? 'Hourly' : ($eng->paying_cycle == 1 ? 'Monthly' : 'Fixed'),
                            number_format($hour->billClient(), 2), $hour->getStatus()[0]]);
                    }
                    $sheet->fromArray($content, null, "A2", true, false);
                });

                $excel->sheet('Expenses($' . number_format($data['bill'][1], 2) . ')', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Consultant', 'Engagement', 'Report Date', 'Company Paid', 'Hotel($)', 'Flight($)', 'Meal($)', 'Office Supply($)', 'Car Rental($)', 'Mileage Cost($)', 'Other($)', 'Total($)', 'Description', 'Status'])
                        ->setAllBorders('thin')
                        ->cells('A1:N1', function ($cells) {
                            $cells->setBackground('#3bd3f9');
                            $cells->setFontFamily('Calibri');
                            $cells->setFontWeight('bold');
                            $cells->setAlignment('center');
                        });
                    $content = [];
                    foreach ($data['expenses'] as $i => $expense) {
                        $eng = $expense->arrangement->engagement;
                        array_push($content, [
                            $expense->consultant->fullname(), $eng->name, $expense->report_date, $expense->company_paid ? 'Yes' : 'No', $expense->hotel, $expense->flight, $expense->meal, $expense->office_supply, $expense->car_rental, $expense->mileage_cost, $expense->other,
                            number_format($expense->total(), 2), $expense->description, $expense->getStatus()[0]
                        ]);
                    }
                    $sheet->fromArray($content, null, "A2", true, false);
                })->setActiveSheetIndex(0);

            })->export('xlsx');


        } else
            return $all ? Excel::create($data['filename'], function ($excel) use ($data) {
                $excel->setTitle('Payroll Overview')
                    ->setCreator('Hao Xiong')
                    ->setCompany('New Life CFO')
                    ->setDescription('All the Payroll under the specified condition(file name)');
                $excel->sheet('Consultant Payroll', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Name', 'Billable Hours', 'Non-billable Hours', 'Hourly Income($)', 'Expense($)', 'Buz Dev Income($)'])
                        ->setAllBorders('thin')
                        ->cells('A1:F1', function ($cells) {
                            $cells->setBackground('#3bd3f9');
                            $cells->setFontFamily('Calibri');
                            $cells->setFontWeight('bold');
                            $cells->setAlignment('center');
                        });
                    $content = [];
                    foreach ($data['consultants'] as $consultant) {
                        $conid = $consultant->id;
                        $salary = $data['incomes'][$conid];
                        array_push($content, [$consultant->fullname(), $data['hrs'][$conid][0], $data['hrs'][$conid][1], number_format($salary[0], 2), number_format($salary[1], 2), number_format($data['buzIncomes'][$conid], 2)]);
                    }
                    array_push($content, []);
                    array_push($content, ['Hourly Total($)', $data['income'][0], 'Expense Total($)', $data['income'][1], 'Buz Dev Total($)', $data['buz_devs']['total']]);
                    $sheet->fromArray($content, null, "A2", true, false);
                });
            })->export('xlsx') : Excel::create($data['filename'], function ($excel) use ($data) {
                $excel->setTitle('Payroll Overview')
                    ->setCreator('Hao Xiong')
                    ->setCompany('New Life CFO')
                    ->setDescription('Your Payroll under the specified condition(file name)');

                $excel->sheet('Hourly Income($' . number_format($data['income'][0], 2) . ')', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Client', 'Engagement', 'Report Date', 'Position', 'Task', 'Billable Hours', 'Non-billable Hours', 'Rate($)', 'Rate Type', 'Share', 'Income($)', 'Description', 'Status'])
                        ->setAllBorders('thin')
                        ->cells('A1:M1', function ($cells) {
                            $cells->setBackground('#3bd3f9');
                            $cells->setFontFamily('Calibri');
                            $cells->setFontWeight('bold');
                        });
                    $content = [];
                    foreach ($data['hours'] as $i => $hour) {
                        $arr = $hour->arrangement;
                        $eng = $arr->engagement;
                        array_push($content, [$hour->client->name, $eng->name, $hour->report_date, $arr->position->name, $hour->task->description, number_format($hour->billable_hours, 2), number_format($hour->non_billable_hours, 2), $hour->rate, $hour->rate_type == 0 ? 'Billing' : 'Pay',
                            number_format($hour->share * 100, 1) . '%', number_format($hour->earned(), 2), $hour->description, $hour->getStatus()[0]]);
                    }
                    $sheet->fromArray($content, null, "A2", true, false);
                });

                $excel->sheet('Expenses($' . number_format($data['income'][1], 2) . ')', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Client', 'Engagement', 'Report Date', 'Company Paid', 'Hotel($)', 'Flight($)', 'Meal($)', 'Office Supply($)', 'Car Rental($)', 'Mileage Cost($)', 'Other($)', 'Total($)', 'Description', 'Status'])
                        ->setAllBorders('thin')
                        ->cells('A1:N1', function ($cells) {
                            $cells->setBackground('#3bd3f9');
                            $cells->setFontFamily('Calibri');
                            $cells->setFontWeight('bold');
                            $cells->setAlignment('center');
                        });
                    $content = [];
                    foreach ($data['expenses'] as $i => $expense) {
                        $eng = $expense->arrangement->engagement;
                        array_push($content, [
                            $expense->client->name, $eng->name, $expense->report_date, $expense->company_paid ? 'Yes' : 'No', $expense->hotel, $expense->flight, $expense->meal, $expense->office_supply, $expense->car_rental, $expense->mileage_cost, $expense->other,
                            number_format($expense->total(), 2), $expense->description, $expense->getStatus()[0]
                        ]);
                    }
                    $sheet->fromArray($content, null, "A2", true, false);
                });

                $excel->sheet('Business Dev($' . number_format($data['buz_devs']['total'], 2) . ')', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Client', 'Engagement', 'Engagement State', 'Buz Dev Share(%)', 'Total Billable Hours', 'Earned'])
                        ->setAllBorders('thin')
                        ->cells('A1:F1', function ($cells) {
                            $cells->setBackground('#3bd3f9');
                            $cells->setFontFamily('Calibri');
                            $cells->setFontWeight('bold');
                            $cells->setAlignment('center');
                        });
                    $content = [];
                    foreach ($data['buz_devs']['engs'] as $i => $eng) {
                        array_push($content, [
                            $eng[0]->client->name, $eng[0]->name, $eng[0]->state(),
                            number_format($eng[0]->buz_dev_share * 100, 1), $eng[2], number_format($eng[1], 2)
                        ]);
                    }
                    $sheet->fromArray($content, null, "A2", true, false);
                })->setActiveSheetIndex(0);
            })->export('xlsx');
    }

    private function filename($entity, $start, $end, $state, $eid, $type)
    {
        $eng = Engagement::find($eid[0]);
        $engname = $eid[0] ? '(' . $eng->client->name . ')' . $eng->name : 'ALL';
        $status = isset($state) ? ($state == '1' ? 'Approved' : 'Pending') : 'ALL';
        return $type . '_' . (isset($entity) ? ($type == 'PAYROLL' ? $entity->fullname() : $entity->name) : 'ALL') . '_START-' . $start . '_END-' . $end . '_STATUS-' . $status . '_ENGAGEMENT-' . $engname;
    }

}
