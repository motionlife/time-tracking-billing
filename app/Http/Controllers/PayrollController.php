<?php

namespace newlifecfo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use newlifecfo\Models\Consultant;
use newlifecfo\Models\Engagement;
use newlifecfo\Models\Expense;
use newlifecfo\Models\Hour;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verifiedConsultant');
    }

    public function index(Request $request, $isAdmin = false)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $eid = $request->get('eid');
        $state = $request->get('state');
        $file = $request->get('file');
        //todo: let user select multiple engagements
        $user = Auth::user();
        $consultant = $isAdmin ? ($request->get('conid') ? Consultant::find($request->get('conid')) : null) : $user->consultant;
        if ($consultant) {
            $hourReports = Hour::recentReports($start, $end, $eid, $consultant, $state);
            $expenseReports = Expense::recentReports($start, $end, $eid, $consultant, $state);
            $hours = $this->paginate($hourReports, $request->get('perpage') ?: 20, $request->get('tab') == 2 ?: $request->get('page'));
            $expenses = $this->paginate($expenseReports, $request->get('perpage') ?: 20, $request->get('tab') != 2 ?: $request->get('page'));
            $buz_devs = $this->getBuzDev($consultant, $start, $end, $eid, $state);
            $income = $this->getIncome($consultant, $start, $end, $eid, $state);

            if ($file == 'excel') return $this->exportExcel(['hours' => $hourReports, 'expenses' => $expenseReports, 'buz_devs' => $buz_devs, 'income' => $income,
                'filename' => $this->filename($consultant, $start, $end, $state, $eid)]);

            return view('wage', ['clientIds' => Engagement::groupedByClient($consultant),
                    'hours' => $hours, 'expenses' => $expenses,
                    'income' => $income,
                    'buz_devs' => $buz_devs,
                    'admin' => $isAdmin,
                    'consultant' => $consultant]
            );

        } else {
            $incomes = [];
            $buz_dev_incomes = [];
            $consultants = Consultant::all();
            foreach ($consultants as $consultant) {
                $incomes[$consultant->id] = $this->getIncome($consultant, $start, $end, $eid, $state);
                $buz_dev_incomes[$consultant->id] = $this->getBuzDev($consultant, $start, $end, $eid, $state)['total'];
            }

            if ($file == 'excel') return $this->exportExcel($consultants);

            return view('wage', ['clientIds' => Engagement::groupedByClient(null),
                'admin' => $isAdmin,
                'consultants' => $consultants,
                'incomes' => $incomes,
                'buzIncomes' => $buz_dev_incomes
            ]);
        }
    }

    //todo : dealing with status query request
    private function getIncome(Consultant $consultant, $start, $end, $eid, $state)
    {
        $total_bh = 0;
        $total_ex = 0;
        foreach ($consultant->arrangements as $arr) {
            if (!isset($eid) || $arr->engagement_id == $eid) {
                $total_bh += $arr->hoursIncomeForConsultant($start, $end, $state);
                $total_ex += $arr->reportedExpenses($start, $end, $state);
            }
        }
        return [$total_bh, $total_ex];
    }

    private function getBuzDev(Consultant $consultant, $start, $end, $eid, $state)
    {
        $total = 0;
        $engs = [];
        foreach ($consultant->dev_clients as $dev_client) {
            foreach ($dev_client->engagements as $engagement) {
                if (!isset($eid) || $engagement->id == $eid) {
                    $devs = $engagement->incomeForBuzDev($start, $end, $state);
                    if ($devs) {
                        array_push($engs, [$engagement, $devs]);
                        $total += $devs;
                    }
                }
            }
        }
        return ['total' => $total, 'engs' => $engs];
    }

    private function exportExcel($data)
    {
        return Excel::create($data['filename'], function ($excel) use ($data) {
            $excel->setTitle('Payroll Overview')
                ->setCreator('Hao Xiong')
                ->setCompany('New Life CFO')
                ->setDescription('Your Payroll under the specified condition(file name)');

            $excel->sheet('Hourly Income($'.number_format($data['income'][0],2).')', function ($sheet) use ($data) {
                $sheet->freezeFirstRow()
                    ->row(1, ['Client', 'Engagement', 'Report Date', 'Billable Hours', 'Non-billable Hours', 'Income($)', 'Description', 'Status'])
                    ->setAllBorders('thin')
                    ->cells('A1:H1', function($cells) {
                        $cells->setBackground('#3bd3f9');
                        $cells->setFontFamily('Calibri');
                        $cells->setFontWeight('bold');
                    });
                foreach ($data['hours'] as $i => $hour) {
                    $arr = $hour->arrangement;
                    $eng = $arr->engagement;
                    $sheet->row($i + 2, [
                        $eng->client->name, $eng->name, $hour->report_date, $hour->billable_hours, $hour->non_billable_hours,
                        number_format($hour->billable_hours * $arr->billing_rate * (1 - $arr->firm_share), 2), $hour->description, $hour->getStatus()[0]
                    ]);
                }
            });

            $excel->sheet('Expenses($'.number_format($data['income'][1],2).')', function ($sheet) use ($data) {
                $sheet->freezeFirstRow()
                    ->row(1, ['Client', 'Engagement', 'Report Date', 'Company Paid', 'Hotel($)', 'Flight($)', 'Meal($)', 'Office Supply($)','Car Rental($)','Mileage Cost($)','Other($)','Total($)','Description','Status'])
                    ->setAllBorders('thin')
                    ->cells('A1:N1', function($cells) {
                        $cells->setBackground('#3bd3f9');
                        $cells->setFontFamily('Calibri');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                    });
                foreach ($data['expenses'] as $i => $expense) {
                    $arr = $expense->arrangement;
                    $eng = $arr->engagement;
                    $sheet->row($i + 2, [
                        $eng->client->name, $eng->name, $expense->report_date, $expense->company_paid?'Yes':'No', $expense->hotel,$expense->flight,$expense->meal,$expense->office_supply,$expense->car_rental,$expense->mileage_cost,$expense->other,
                        number_format($expense->total(), 2), $expense->description, $expense->getStatus()[0]
                    ]);
                }

            });

            $excel->sheet('Business Dev($'.number_format($data['buz_devs']['total'],2).')', function ($sheet) use ($data) {
                $sheet->freezeFirstRow()
                    ->row(1, ['Client', 'Engagement', 'Engagement State', 'Buz Dev Share(%)', 'Earned'])
                    ->setAllBorders('thin')
                    ->cells('A1:E1', function($cells) {
                        $cells->setBackground('#3bd3f9');
                        $cells->setFontFamily('Calibri');
                        $cells->setFontWeight('bold');
                        $cells->setAlignment('center');
                    });
                foreach ($data['buz_devs']['engs'] as $i => $eng) {
                    $sheet->row($i + 2, [
                        $eng[0]->client->name, $eng[0]->name,$eng[0]->state(),
                        number_format($eng[0]->buz_dev_share*100,1),number_format($eng[1],2)
                    ]);
                }
            })->setActiveSheetIndex(0);
        })->export('xlsx');
    }

    private function filename($consultant, $start, $end, $state, $eid)
    {
        $eng = Engagement::find($eid);
        $engname = isset($eng) ? '(' . $eng->client->name . ')' . $eng->name : 'ALL';
        $status = isset($state) ? ($state == '1' ? 'Approved' : 'Pending') : 'ALL';
        return 'PAYROLL_' . $consultant->fullname() . '_START-' . $start . '_END-' . $end . '_STATUS-' . $status . '_ENGAGEMENT-' . $engname;
    }
}
