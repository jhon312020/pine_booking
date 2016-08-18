<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use App\Expense;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Repositories\ExpenseRepository;

class HomeController extends Controller
{
    /**
     * The expense repository instance.
     * 
     * @var ExpenseRespository 
     */
    protected $expenses;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ExpenseRepository $expenses)
    {
        $this->middleware('auth');
        $this->expenses = $expenses;
    }

    /**
     * Displays the application dashboard.
     *
     * @param Request $request
     * @param Expense $expense
     * @return void
     */
    public function index(Request $request, Expense $expense)
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime("-1 days"));
        $month = date('m');
        $today_expenses = $this->expenses->expensesOfCompany($expense, $today, $today);
        $yesterday_expenses = $this->expenses->expensesOfCompany($expense, $yesterday, $yesterday);
        $expenses['total_no_expenses_for_today'] = count($today_expenses);
        $expenses['total_expenses_for_today'] = $today_expenses->sum('amount');
        $expenses['total_no_expenses_for_yesterday'] = count($yesterday_expenses);
        $expenses['total_expenses_for_yesterday'] = $yesterday_expenses->sum('amount');
        $expenses_grouped_for_user = $this->expenses->expensesOfCompanyGroupedByDate($expense, $month);
        $expenses_grouped = $expenses_grouped_for_user->toArray();
        $expenses_data = array_combine(array_column($expenses_grouped_for_user->toArray(), 'day'), array_column($expenses_grouped_for_user->toArray(), 'totalamount'));
        $last_day_this_month  = date('d');
        $total_expense_of_month = 0;
        for ($day = 1; $day <=$last_day_this_month; $day++) {
            $process_date = date("Y-m-$day");
            $show_date = date('d-m-Y', strtotime($process_date));
            if (array_key_exists($day, $expenses_data)) {
                $expenses_data_for_month[] = array('Day'=>"$show_date",'value'=>$expenses_data[$day]);
                $total_expense_of_month += $expenses_data[$day];
            } else {
                $expenses_data_for_month[] = array('Day'=>"$show_date",'value'=>0);
            }
        }
        $expenses_grap_data = json_encode($expenses_data_for_month);
        return view('home', [
            'expenses' => $expenses,
            'expenses_grap_data' => $expenses_grap_data,
            'total_expense_of_month' => $total_expense_of_month,
        ]);
    }
}
