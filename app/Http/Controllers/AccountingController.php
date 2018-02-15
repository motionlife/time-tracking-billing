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
    const ACCOUNTING_FORMAT = '_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)';

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
                if ($file == 'excel') {
                    $data = [];
                    foreach (Client::all() as $client) {
                        $id = $client->id;
                        $data['bill'][$id] = [$client->name, $client->constructBills($start, $end, $state, $eid)];
                    }
                    return $this->exportExcel(array_add($data, 'filename', $this->filename(null, $start, $end, $state, $eid, 'Bill')), true, true);
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

                    $data = ['admin' => $isAdmin,
                        'clients' => $clients,
                        'bills' => $bills,
                        'hrs' => $hourNumbers,
                        'bill' => [$sum[0], $sum[1]]];
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
                    $rowNum = 1;
                    $sheet->freezeFirstRow()
                        ->row($rowNum++, ['Client', 'Engagement', 'Position', 'Consultant', 'Billable Hours', 'Non-billable Hours', 'Billing Rate', 'Billed Type', 'Engagement Bill', 'Expense Bill', 'Total'])
                        ->cells('A1:K1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
                        })->setColumnFormat(['E:F' => '0.00', 'G' => self::ACCOUNTING_FORMAT, 'I:K' => self::ACCOUNTING_FORMAT]);
                    $BTotal = 0;
                    $NbTotal = 0;
                    $EngTotal = 0;
                    $ExpTotal = 0;
                    $Total = 0;
                    foreach ($data['bill'] as $cid => $clientBill) {
                        if ($clientBill[1]->count()) {
                            $sheet->row($rowNum++, [$clientBill[0]]);
                            $cBTotal = 0;
                            $cNbTotal = 0;
                            $cEngTotal = 0;
                            $cExpTotal = 0;
                            $cTotal = 0;
                            foreach ($clientBill[1] as $eid => $engGroups) {
                                if ($engGroups->count() > 0) {
                                    $sheet->row($rowNum++, [null, $engGroups[0]['ename']]);
                                    $engTotal = 0;
                                    foreach ($engGroups as $bill) {
                                        $rowTotal = $bill['engBill'] + $bill['expBill'];
                                        $engTotal += $rowTotal;
                                        $sheet->row($rowNum, [null, null, $bill['position'], $bill['consultant'], $bill['bhours'], $bill['nbhours'], $bill['brate'], $bill['bType'], $bill['engBill'], $bill['expBill'], $rowTotal]);
                                        $billedType = $bill['bType'];
                                        if ($billedType && $billedType != 'Hourly') {
                                            $sheet->cells('C' . $rowNum . ':H' . $rowNum, function ($cells) use ($billedType) {
                                                $cells->setFontColor($billedType == 'Monthly Retainer' ? '#1180ff':'#ff0000')->setFontWeight('bold');
                                            });
                                        }
                                        $rowNum++;
                                    }
                                    $engBh = $engGroups->sum('bhours');
                                    $engNbh = $engGroups->sum('nbhours');
                                    $engBill = $engGroups->sum('engBill');
                                    $engExp = $engGroups->sum('expBill');
                                    $cBTotal += $engBh;
                                    $cNbTotal += $engNbh;
                                    $cEngTotal += $engBill;
                                    $cExpTotal += $engExp;
                                    $cTotal += $engTotal;
                                    $sheet->row($rowNum, [null, $engGroups[0]['ename'] . ' Total', null, null, $engBh, $engNbh, null, null, $engBill, $engExp, $engTotal])
                                        ->cells('B' . $rowNum . ':K' . $rowNum, function ($cells) {
                                            $cells->setBackground('#e1e3e8')->setFontWeight('bold');
                                        });
                                    $rowNum++;
                                }
                            }
                            $BTotal += $cBTotal;
                            $NbTotal += $cNbTotal;
                            $EngTotal += $cEngTotal;
                            $ExpTotal += $cExpTotal;
                            $Total += $cTotal;
                            $sheet->row($rowNum, [$clientBill[0] . ' Total', null, null, null, $cBTotal, $cNbTotal, null, null, $cEngTotal, $cExpTotal, $cTotal])
                                ->cells('A' . $rowNum . ':K' . $rowNum, function ($cells) {
                                    $cells->setBackground('#befcab')->setFontWeight('bold');
                                });
                            $rowNum++;
                        }
                    }
                    $rowNum++;
                    $sheet->row($rowNum, ['Hourly Total', null, null, null, $BTotal, $NbTotal])
                        ->row($rowNum + 1, ['Dollar Total', null, null, null, null, null, null, null, $EngTotal, $ExpTotal, $Total])
                        ->cells('A' . $rowNum . ':K' . ($rowNum + 1), function ($cells) {
                            $cells->setBackground('#7cfcff')->setFontWeight('bold')->setFontSize('12');
                        });
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
                        })->setColumnFormat(['F' => '0.00', 'G' => self::ACCOUNTING_FORMAT, 'J' => self::ACCOUNTING_FORMAT]);
                    foreach ($data['hours'] as $i => $hour) {
                        $arr = $hour->arrangement;
                        $eng = $arr->engagement;
                        if ($hour->rate_type == 0) {
                            $sheet->appendRow([$hour->consultant->fullname(), $eng->name, $hour->report_date, $arr->position->name, $hour->task->description, $hour->billable_hours, $hour->rate, $hour->rate_type == 0 ? 'Billing rate' : 'Pay rate', $eng->paying_cycle == 0 ? 'Hourly' : ($eng->paying_cycle == 1 ? 'Monthly' : 'Fixed'), $hour->billClient(), $hour->getStatus()[0]]);
                        }
                    }
                });
                $excel->sheet('Non-hourly Eng.($' . number_format($nheng_total, 2) . ')', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Engagement', 'Billed Type', 'Started Date', 'Closed Date', 'Status', 'Billed Amount($)'])
                        ->cells('A1:F1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
                        })->setColumnFormat(['F' => self::ACCOUNTING_FORMAT]);
                    foreach ($data['fm_engagements'] as $i => $eng) {
                        $sheet->appendRow([$eng[0]->name, $eng[0]->clientBilledType(), $eng[0]->start_date, $eng[0]->close_date, $eng[0]->state(), $eng[1]]);
                    }
                });
                $excel->sheet('Expenses($' . number_format($data['bill'][1], 2) . ')', function ($sheet) use ($data) {
                    $sheet->freezeFirstRow()
                        ->row(1, ['Consultant', 'Engagement', 'Report Date', 'Company Paid', 'Hotel($)', 'Flight($)', 'Meal($)', 'Office Supply($)', 'Car Rental($)', 'Mileage Cost($)', 'Other($)', 'Total($)', 'Description', 'Status'])
                        ->cells('A1:N1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
                        })->setColumnFormat(['E:L' => self::ACCOUNTING_FORMAT]);
                    foreach ($data['expenses'] as $i => $expense) {
                        $eng = $expense->arrangement->engagement;
                        $sheet->appendRow([$expense->consultant->fullname(), $eng->name, $expense->report_date, $expense->company_paid ? 'Yes' : 'No', $expense->hotel, $expense->flight, $expense->meal, $expense->office_supply, $expense->car_rental, $expense->mileage_cost, $expense->other, $expense->total(), $expense->description, $expense->getStatus()[0]]);
                    }
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
                        })->setColumnFormat(['E:F' => '0.00', 'G:J' => self::ACCOUNTING_FORMAT, 'K' => '0.0%', 'L:M' => self::ACCOUNTING_FORMAT]);
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
                    $sheet->setColumnFormat(['F:G' => '0.00', 'H' => self::ACCOUNTING_FORMAT, 'J' => '0.0%', 'K' => self::ACCOUNTING_FORMAT])->freezeFirstRow()
                        ->row(1, ['Client', 'Engagement', 'Report Date', 'Position', 'Task', 'Billable Hours', 'Non-billable Hours', 'Rate', 'Rate Type', 'Share', 'Income', 'Description', 'Status'])
                        ->cells('A1:M1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
                        });
                    foreach ($data['hours'] as $i => $hour) {
                        $arr = $hour->arrangement;
                        $eng = $arr->engagement;
                        $sheet->appendRow([$hour->client->name, $eng->name, $hour->report_date, $arr->position->name, $hour->task->description, $hour->billable_hours, $hour->non_billable_hours, $hour->rate, $hour->rate_type == 0 ? 'Billing' : 'Pay',
                            $hour->share, $hour->earned(), $hour->description, $hour->getStatus()[0]]);
                    }
                });
                $excel->sheet('Expenses($' . number_format($data['income'][1], 2) . ')', function ($sheet) use ($data) {
                    $sheet->setColumnFormat(['E:L' => self::ACCOUNTING_FORMAT])->freezeFirstRow()
                        ->row(1, ['Client', 'Engagement', 'Report Date', 'Company Paid', 'Hotel', 'Flight', 'Meal', 'Office Supply', 'Car Rental', 'Mileage Cost', 'Other', 'Total', 'Description', 'Status'])
                        ->cells('A1:N1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
                        });
                    foreach ($data['expenses'] as $i => $expense) {
                        $eng = $expense->arrangement->engagement;
                        $sheet->appendRow([$expense->client->name, $eng->name, $expense->report_date, $expense->company_paid ? 'Yes' : 'No', $expense->hotel, $expense->flight, $expense->meal, $expense->office_supply, $expense->car_rental, $expense->mileage_cost, $expense->other,
                            $expense->payConsultant(), $expense->description, $expense->getStatus()[0]
                        ]);
                    }
                });
                $excel->sheet('Business Dev($' . number_format($data['buz_devs']['total'], 2) . ')', function ($sheet) use ($data) {
                    $sheet->setColumnFormat(['D' => '0.0%', 'E:F' => self::ACCOUNTING_FORMAT])->freezeFirstRow()
                        ->row(1, ['Client', 'Engagement', 'Engagement State', 'Buz Dev Share(%)', 'Engagement Bill', 'Earned'])
                        ->cells('A1:F1', function ($cells) {
                            $this->setTitleCellsStyle($cells);
                        });
                    foreach ($data['buz_devs']['engs'] as $i => $eng) {
                        $sheet->appendRow([
                            $eng[0]->client->name, $eng[0]->name, $eng[0]->state(),
                            $eng[0]->buz_dev_share, $eng[1] / $eng[0]->buz_dev_share, $eng[1]
                        ]);
                    }
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
