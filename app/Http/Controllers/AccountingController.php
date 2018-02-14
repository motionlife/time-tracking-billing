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
        $tab = $request->get('tab');
        $perpage = $request->get('perpage');
        $page = $request->get('page');
        if ($payroll) {
            $consultant = $isAdmin ? ($request->get('conid') ? Consultant::find($request->get('conid')) : null) : $user->consultant;
            if ($consultant) {
                $hourReports = Hour::reported($start, $end, $eid, $consultant, $state);
                $expenseReports = Expense::reported($start, $end, $eid, $consultant, $state)->filter(function ($value) {
                    return !$value->company_paid;
                });
                $pg_hours = $this->paginate($hourReports, $perpage ?: 20, $tab == 2 ?: $page);
                $pg_expenses = $this->paginate($expenseReports, $perpage ?: 20, $tab != 2 ?: $page);
                $income = [$hourReports->sum(function ($hour) {
                    return $hour->earned();
                }), $expenseReports->sum(function ($exp) {
                    return $exp->payConsultant();
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
                if ($file == 'excel') {
                    $data = [];
                    $consultants = Consultant::recognized();
                    foreach ($consultants as $consultant) {
                        $id = $consultant->id;
                        $data['payroll'][$id] = [$consultant->fullname(), $consultant->getPayroll($start, $end, $state, $eid)];
                    }
                    return $this->exportExcel(array_add($data, 'filename', $this->filename(null, $start, $end, $state, $eid, 'PAYROLL')), true);
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
                    $data = ['admin' => $isAdmin,
                        'consultants' => $consultants,
                        'incomes' => $incomes,
                        'buzIncomes' => $buz_dev_incomes,
                        'hrs' => $hourNumbers,
                        'income' => [$sum[0], $sum[1]],
                        'buz_devs' => ['total' => $sum[2]]];
                    return view('wage', array_add($data, 'clientIds', Engagement::groupedByClient(null)));
                }
            }
        } else {
            //client billing controller
            $client = Client::find($client_id);
            if ($client) {
                //bill for the specific client
                $engagementBill = $client->engagementBill($start, $end, $state, $eid);
                $expenseBill = $client->expenseBill($start, $end, $state, $eid);
                $pg_hours = $this->paginate($engagementBill[1], $perpage ?: 20, $tab == 2 || $tab == 3 ?: $page);
                $fm_engagments = $this->paginate($engagementBill['NonHourlyEngagement'], $perpage ?: 20, $tab != 2 ?: $page);
                $pg_expenses = $this->paginate($expenseBill[1], $perpage ?: 20, $tab != 3 ?: $page);
                $bill = [$engagementBill[0], $expenseBill[0]];

                if ($file == 'excel') return $this->exportExcel(['hours' => $engagementBill[1], 'fm_engagements' => $engagementBill['NonHourlyEngagement'], 'expenses' => $expenseBill[1], 'buz_devs' => null, 'bill' => $bill,
                    'filename' => $this->filename($client, $start, $end, $state, $eid, 'Bill')], false, true);

                return view('bill', ['clientIds' => $client->getEngagementIdName(),
                        'hours' => $pg_hours, 'expenses' => $pg_expenses,
                        'bill' => $bill,
                        'fm_engagements' => $fm_engagments,
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
        $engagementBill = $client->engagementBill($start, $end, $state, $eid);;
        $expenseBill = $client->expenseBill($start, $end, $state, $eid);
        $hrs[0] = $engagementBill[2];//hours only in the hourly-engagement
        $hrs[1] = $engagementBill[3];
        return [$engagementBill[0], $expenseBill[0]];
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
            return $exp->payConsultant();
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
//                        $tbh = 0;
//                        foreach ($engagement->arrangements()->withTrashed()->get() as $arrangement) {
//                            foreach ($arrangement->hours as $hour) {
//                                $tbh += $hour->billable_hours;
//                            }
//                        }
//                        array_push($engs, [$engagement, $devs, $tbh]);
                        array_push($engs, [$engagement, $devs]);
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
            return $all ? Excel::create($data['filename'], function ($excel) use ($data) {
                $this->setExcelProperties($excel, 'Billing Overview');
                $excel->sheet('Client Bill', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Client', 'Billable Hours', 'Non-billable Hours', 'Engagement Bill($)', 'Expense Bill($)', 'Total($)'])
                        ->cells('A1:F1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
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
                $this->setExcelProperties($excel, 'Billing Overview');
                $nheng_total = $data['fm_engagements']->sum(function ($comb) {
                    return $comb[1];
                });
                $excel->sheet('Hourly Eng.($' . number_format($data['bill'][0] - $nheng_total, 2) . ')', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Consultant', 'Engagement', 'Report Date', 'Position', 'Task', 'Billable Hours', 'Rate($)', 'Rate Type', 'Billed Type', 'Billed($)', 'Report Status'])
                        ->cells('A1:K1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
                        });
                    $content = [];
                    foreach ($data['hours'] as $i => $hour) {
                        $arr = $hour->arrangement;
                        $eng = $arr->engagement;
                        if ($hour->rate_type == 0) {
                            array_push($content, [$hour->consultant->fullname(), $eng->name, $hour->report_date, $arr->position->name, $hour->task->description, number_format($hour->billable_hours, 2), $hour->rate, $hour->rate_type == 0 ? 'Billing rate' : 'Pay rate', $eng->paying_cycle == 0 ? 'Hourly' : ($eng->paying_cycle == 1 ? 'Monthly' : 'Fixed'),
                                number_format($hour->billClient(), 2), $hour->getStatus()[0]]);
                        }
                    }
                    $sheet->fromArray($content, null, "A2", true, false);
                });
                $excel->sheet('Non-hourly Eng.($' . number_format($nheng_total, 2) . ')', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Engagement', 'Billed Type', 'Started Date', 'Closed Date', 'Status', 'Billed Amount($)'])
                        ->cells('A1:F1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
                        });
                    $content = [];
                    foreach ($data['fm_engagements'] as $i => $eng) {
                        array_push($content, [$eng[0]->name, $eng[0]->clientBilledType(), $eng[0]->start_date, $eng[0]->close_date, $eng[0]->state(), number_format($eng[1], 2)]);
                    }
                    $sheet->fromArray($content, null, "A2", true, false);
                });
                $excel->sheet('Expenses($' . number_format($data['bill'][1], 2) . ')', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Consultant', 'Engagement', 'Report Date', 'Company Paid', 'Hotel($)', 'Flight($)', 'Meal($)', 'Office Supply($)', 'Car Rental($)', 'Mileage Cost($)', 'Other($)', 'Total($)', 'Description', 'Status'])
                        ->cells('A1:N1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
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
        } else {
            return $all ? Excel::create($data['filename'], function ($excel) use ($data) {
                $this->setExcelProperties($excel, 'Payroll Overview');
                $excel->sheet('Consultant Payroll', function ($sheet) use ($data) {
                    $rowNum = 1;
                    $sheet->freezeFirstRow()
                        ->row($rowNum++, ['Name', 'Engagement Name', 'Engagement Lead', 'Position', 'Billable Hours', 'Non-billable Hours', 'Billing Rate', 'Pay Rate', 'Hourly Pay', 'Expense', 'Biz Dev %', 'Biz Dev Income', 'Grand Total'])
                        ->cells('A1:M1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
                        })->setColumnFormat([
                                'G:J' => '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
                                'K' => '0%',
                                'L:M' => '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)',
                            ]
                        );
                    $BTotal = 0;
                    $NbTotal = 0;
                    $HpTotal = 0;
                    $ExpTotal = 0;
                    $BizTotal = 0;
                    $Total = 0;
                    foreach ($data['payroll'] as $id => $payroll) {
                        if ($payroll[1]->count()) {
                            $sheet->row($rowNum++, [$payroll[0]]);
                            $gTotal = 0;
                            foreach ($payroll[1] as $aid => $pay) {
                                //$rowSum = (isset($pay['hourlyPay']) ? $pay['hourlyPay'] : 0) + (isset($pay['expense']) ? $pay['expense'] : 0) + (isset($pay['bizDevIncome']) ? $pay['bizDevIncome'] : 0);
                                $rowSum = $pay['hourlyPay'] + $pay['expense'] + $pay['bizDevIncome'];
                                $gTotal += $rowSum;
                                $sheet->row($rowNum, [null, $pay['ename'], $pay['elead'], $pay['position'], $pay['bhours'], $pay['nbhours'], $pay['brate'], $pay['prate'], $pay['hourlyPay'], $pay['expense'], $pay['bizDevShare'], $pay['bizDevIncome'], $rowSum]);
                                if ($aid < 0) {
                                    $sheet->cells('A' . $rowNum . ':M' . $rowNum, function ($cells) {
                                        $cells->setFontColor('#ff0000');
                                    });
                                } else if ($pay['bizDevShare'] > 0 || $pay['bizDevIncome'] > 0) {
                                    $sheet->cells('K' . $rowNum . ':L' . $rowNum, function ($cells) {
                                        $cells->setBackground('##faff02');
                                    });
                                }
                                $rowNum++;
                            }
                            $bhours = $payroll[1]->sum('bhours');
                            $nbhours = $payroll[1]->sum('nbhours');
                            $hourpay = $payroll[1]->sum('hourlyPay');
                            $expense = $payroll[1]->sum('expense');
                            $bizdev = $payroll[1]->sum('bizDevIncome');
                            $BTotal += $bhours;
                            $NbTotal += $nbhours;
                            $HpTotal += $hourpay;
                            $ExpTotal += $expense;
                            $BizTotal += $bizdev;
                            $Total += $gTotal;
                            $sheet->row($rowNum, [$payroll[0] . ' Total', null, null, null, $bhours, $nbhours, null, null, $hourpay, $expense, null, $bizdev, $gTotal])
                                ->cells('A' . $rowNum . ':M' . $rowNum, function ($cells) {
                                    $cells->setBackground('#c5cddb')->setFontWeight('bold');
                                });
                            $rowNum++;
                        }
                    }
                    $rowNum++;
                    $sheet->row($rowNum, ['Hourly Total', null, null, null, $BTotal, $NbTotal])
                        ->row($rowNum + 1, ['Dollar Total', null, null, null, null, null, null, null, $HpTotal, $ExpTotal, null, $BizTotal, $Total])
                        ->cells('A' . $rowNum . ':M' . ($rowNum + 1), function ($cells) {
                            $cells->setBackground('#bfffe8')->setFontWeight('bold')->setFontSize('12');
                        });
                });
            })->export('xlsx') : Excel::create($data['filename'], function ($excel) use ($data) {
                $this->setExcelProperties($excel, 'Payroll Overview');
                $excel->sheet('Hourly Income($' . number_format($data['income'][0], 2) . ')', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Client', 'Engagement', 'Report Date', 'Position', 'Task', 'Billable Hours', 'Non-billable Hours', 'Rate($)', 'Rate Type', 'Share', 'Income($)', 'Description', 'Status'])
                        ->cells('A1:M1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
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
                        ->cells('A1:N1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
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
                        ->row(1, ['Client', 'Engagement', 'Engagement State', 'Buz Dev Share(%)', 'Engagement Bill', 'Earned'])
                        ->cells('A1:F1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
                        });
                    $content = [];
                    foreach ($data['buz_devs']['engs'] as $i => $eng) {
                        array_push($content, [
                            $eng[0]->client->name, $eng[0]->name, $eng[0]->state(),
                            number_format($eng[0]->buz_dev_share * 100, 1), number_format($eng[1] / $eng[0]->buz_dev_share, 2), number_format($eng[1], 2)
                        ]);
                    }
                    $sheet->fromArray($content, null, "A2", true, false);
                })->setActiveSheetIndex(0);
            })->export('xlsx');
        }
    }

    private function filename($entity, $start, $end, $state, $eid, $type)
    {
        $eng = Engagement::find($eid[0]);
        $engname = $eid[0] ? '(' . $eng->client->name . ')' . $eng->name : 'ALL';
        $status = isset($state) ? ($state == '1' ? 'Approved' : 'Pending') : 'ALL';
        return $type . '_' . (isset($entity) ? ($type == 'PAYROLL' ? $entity->fullname() : $entity->name) : 'ALL') . '_START-' . $start . '_END-' . $end . '_STATUS-' . $status . '_ENGAGEMENT-' . $engname;
    }

    private function setExcelProperties($excel, $title)
    {
        $excel->setTitle($title)
            ->setCreator('Hao Xiong')
            ->setCompany('New Life CFO')
            ->setDescription('The filtering condition is in file name');
    }

    private function setTitleCellsStyle($cells)
    {
        $cells->setBackground('#3bd3f9')->setFontFamily('Calibri')->setFontWeight('bold')->setAlignment('center');
    }
}
