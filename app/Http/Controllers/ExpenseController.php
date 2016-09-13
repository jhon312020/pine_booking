<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Http\Requests;
use App\Expense;
use App\Income;
use App\ReservationAdvance;
use DB;
use App\Http\Controllers\Controller;
use App\Repositories\ExpenseRepository;
use App\Repositories\IncomeRepository;

class ExpenseController extends Controller
{
    /**
     * The expense repository instance.
     * 
     * @var ExpenseRespository 
     */
    protected $expenses;
    
    /**
     * The income repository instance.
     * 
     * @var IncomeRepository 
     */
    protected $incomes;
    
    /**
     * string.
     * 
     * @var IncomeRepository 
     */
    protected $role;
    /*
    *
    *@var food category
    */
    protected $food_category = array('tea' => 'Tea', 'snacks' => 'Snacks');
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ExpenseRepository $expenses, IncomeRepository $incomes)
    {
        $this->middleware('auth');
        $this->expenses = $expenses;
        $this->incomes = $incomes;
        if(isset(\Auth::user()->role))
            $this->role = \Auth::user()->role;
    }
    
    /**
     * Displays all the expenses of the company.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, Expense $expense)
    {
        if ($request->isMethod('post')) {
            $expense_date_from = date('Y-m-d', strtotime($request->from_date));
            $expense_date_to = date('Y-m-d', strtotime($request->to_date));
        } else {
            $expense_date_from = $expense_date_to = date('Y-m-d');
        }
        $expense_object = $this->expenses->allExpenses($expense, $expense_date_from, $expense_date_to);
        //Works only with php 5.5 above
        $total_expenses = array_sum(array_column($expense_object->toArray(), 'amount'));
        return view('expenses/'.$this->role.'/index',[
            'expenses' => $expense_object,
            'page_title' => 'OverAll Expenses',
            'expense_date_from' => date('d-m-Y', strtotime($expense_date_from)),
            'expense_date_to' => date('d-m-Y', strtotime($expense_date_to)),
            'total_expenses' => number_format($total_expenses, 2)
        ]);
    }
   
    /**
     * Lists all the expenses of the compay.
     *
     * @param Request $request
     * @return Response
     */
    public function listing(Request $request, Expense $expense)
    {
        if ($request->isMethod('post')) {
            $expense_date_from = date('Y-m-d', strtotime($request->date_of_expense_from));
            $expense_date_to = date('Y-m-d', strtotime($request->date_of_expense_to));
        } else {
            $expense_date_from = $expense_date_to = date('Y-m-d');
        }
        //$exp_category = $this->get_expense_category(strtolower($request->route()->getPath()));
        $expense_object = $this->expenses->categoryList($expense, $expense_date_from, $expense_date_to);
        //Works only with php 5.5 above
        $total_expenses = array_sum(array_column($expense_object->toArray(), 'amount'));
        return view('expenses/'.$this->role.'/listing',[
            'expenses' => $expense_object,
          //  'category' => $exp_category,
            'expense_date_from' => date('d-m-Y', strtotime($expense_date_from)),
            'expense_date_to' => date('d-m-Y', strtotime($expense_date_to)),
            'total_expenses' => number_format($total_expenses, 2)
        ]);
    }
    
    /**
     * Allows the user to create a new expense.
     * Works for both GET and POST Methods
     * 
     * @param Request $request
     * @return Response
     */
    public function add(Request $request, Expense $expense)
    {
        $exp_category = $this->get_expense_category(strtolower($request->route()->getPath()));
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'name' => 'required | max:100',
                'date_of_expense' => 'required',
                'amount' => 'required'
            ]);
            
            $request->user()->expenses()->create([
                'name' => $request->name,
                'date_of_expense' => date('Y-m-d', strtotime($request->date_of_expense)),
                'category' => $request->category,
                'amount' => $request->amount,
                'notes' => $request->notes,
            ]);
        }
        $expense_object = $this->expenses->categoryListByCreated($expense, date('Y-m-d'));
        return view('expenses/add', ['category'=>$exp_category, 'expenses' => $expense_object, 'food_category' => $this->food_category]);
    }
    
    /**
     * Allows the logged in user to delete his expense.
     *
     * @param Request $request
     * @param Expense $expense
     * @return void
     */
    public function delete(Request $request, Expense $expense)
    {
        if ($this->authorize('destroy', $expense)) {
            $expense_date = $expense->select('date_of_expense')->where('id', $expense->id)->get();
            $request->session()->put('date_of_expense', $expense_date[0]->date_of_expense);
            $expense->delete();
        }
        return redirect('/expense/'.$this->role.'/list');
    }
    
    /**
     * Allows to view the expenses beloging today
     * or yesterday
     * 
     * @param Request $request
     * @param Expense $expense
     * @return void
     */
    public function today(Request $request, Expense $expense)
    {
        $exp_category = '';
        switch(strtolower($request->route()->getPath())) {
            case 'expense/yesterday':
                $expense_date_from = $expense_date_to = date('Y-m-d', strtotime('-1 day'));
                $exp_category = 'yesterday\'s';
            break;
            case 'expense/today':
                $expense_date_from = $expense_date_to = date('Y-m-d');
                $exp_category = 'today\'s';
            break;
            default:
                $expense_date_from = $expense_date_to = $request->session()->pull('date_of_expense', date('Y-m-d'));
                 $exp_category = 'overall';
        }
        if ($request->isMethod('post')) {
            $expense_date_from = date('Y-m-d', strtotime($request->date_of_expense_from));
            $expense_date_to = date('Y-m-d', strtotime($request->date_of_expense_to));
        } 
        return view('expenses/'.$this->role.'/list',[
            'expenses' => $this->expenses->expensesOfCompany($expense, $expense_date_from, $expense_date_to),
            'expense_date_from' => date('d-m-Y', strtotime($expense_date_from)),
            'expense_date_to' => date('d-m-Y', strtotime($expense_date_to)),
            'category' => $exp_category,
        ]);
        
    }
    
    /**
     * Display the yearly report of the expenses.
     *
     * @param Request $request
     * @param Expense $expense
     * @return void
     */
    public function yearly(Request $request, Expense $expense)
    {
        switch(strtolower($request->route()->getPath())) {
            case 'reports/lastyear':
                $current_month = date("m", mktime(null, null, null, 12));
                $year = date("Y",strtotime("-1 year"));
                $url = array('link_name'=>'Current Year', '0'=>'/reports/currentyear');
            break;
            default:
                $current_month = date('m');
                $year = date('Y');
                $url = array('link_name'=>'Last Year', '0'=>'/reports/lastyear');
        }
        $expenses_grouped_for_user = $this->expenses->expensesOfCompanyGroupedByMonth($expense, $year);
        $expenses_data = array_combine(array_column($expenses_grouped_for_user, 'report_month'), array_column($expenses_grouped_for_user, 'totalamount'));
        $expenses_data_for_month =  array();
        $total_expense_of_year = 0;
        $total_income_of_year = 0;
        $income_of_year = $this->incomes->incomeOfUserGroupedByMonth($request->user(), $year);
        $income_of_year = array_combine(array_column($income_of_year->toArray(), 'month'), array_column($income_of_year->toArray(), 'totalamount'));
       
       /*income for month start*/
        $income = ReservationAdvance::select(DB::raw('sum(paid) as amount'), DB::raw('MONTH(updated_at) as paid_month'))
        ->whereRaw('YEAR(updated_at) = "'.$year.'"')
        ->groupby(DB::raw('MONTH(updated_at)'))->get()->toArray();
        $income_data = array_combine(array_column($income, 'paid_month'), array_column($income, 'amount'));
        
        $total_income_of_year = ReservationAdvance::select(DB::raw('sum(paid) as amount'))
        ->whereRaw('YEAR(updated_at) = "'.$year.'"')
        ->first()->amount;
        /*income for month end*/
       
        for ($month = 1; $month <=$current_month; $month++) {
            $month_name = date("M", mktime(null, null, null, $month));
            $data = array();
            if (array_key_exists($month, $expenses_data)) {
                $data = array('Month'=>"$month_name",'expense'=>$expenses_data[$month], 'income' => isset($income_data[$month])?$income_data[$month]:0);
                $total_expense_of_year += $expenses_data[$month];
            } else {
                $data = array('Month'=>"$month_name",'expense'=>0, 'income' => isset($income_data[$month])?$income_data[$month]:0);
            }
            /*if (array_key_exists($month, $income_of_year)) {
                 $data['income'] = $income_of_year["$month"];
                 $total_income_of_year += $income_of_year["$month"];
            } else {
               $data['income'] = 0;
            }*/
            $expenses_data_for_month[] = $data;
        }
        
        $expenses_grap_data = json_encode($expenses_data_for_month);
        return view('reports/yearly', [
            'expenses_grap_data' => $expenses_grap_data,
            'total_expense_of_year' => $total_expense_of_year,
            'total_income_of_year' => $total_income_of_year,
            'year' => $year,
            'url' => $url
        ]);
    }
    
    /**
     * Displays the monthly report of the expenses.
     *
     * @param Request $request
     * @param Expense $expense
     * @return void
     */
    public function monthly(Request $request, Expense $expense)
    {
        $str_cur_month = date("M");
        if (isset($request->month) && $request->month != $str_cur_month) {
            $string_time = strtotime($request->month);
            $month = date("m", $string_time);
            $str_cur_month = $request->month;
            $last_day_this_month  = date('t', $string_time);
            $month_lead = date("n", $string_time);
        } else {
            $last_day_this_month  = date('d');
            $month = date("m");
            $month_lead = date("n");
        }
        $current_month = date("m");
        
        $expenses_grouped = $this->expenses->expensesOfCompanyGroupedByDate($expense, $month);
        //$expenses_grouped = $expenses_grouped_for_user->toArray();
        $expenses_data = array_combine(array_column($expenses_grouped, 'report_date'), array_column($expenses_grouped, 'totalamount'));
        
        /*income for month start*/
        $income = ReservationAdvance::select(DB::raw('sum(paid) as amount'), DB::raw('DAY(updated_at) as paid_date'))
        ->whereRaw('MONTH(updated_at) = "'.$month_lead.'" and YEAR(updated_at) = YEAR(NOW())')
        ->groupby(DB::raw('DATE(updated_at)'))->get()->toArray();
        $income_data = array_combine(array_column($income, 'paid_date'), array_column($income, 'amount'));
        
        
        $total_income_of_month = $this->incomes->incomeOfUserForMonth($request->user(), $month, date('Y'))->toArray();
        if ($total_income_of_month) {
            $total_income_of_month = $total_income_of_month[0]['totalamount'];
        } else {
            $total_income_of_month = 0;
        }
        
        $total_income_of_month = ReservationAdvance::select(DB::raw('sum(paid) as amount'))
        ->whereRaw('MONTH(updated_at) = "'.$month_lead.'" and YEAR(updated_at) = YEAR(NOW())')
        ->first()->amount;
        /*income for month end*/
        
        //print_r($total_income_of_month);die;
        $total_expense_of_month = 0;
        for ($day = 1; $day <=$last_day_this_month; $day++) {
            $process_date = date("Y-$month-$day");
            $show_date = date('d-m-Y', strtotime($process_date));
            if (array_key_exists($day, $expenses_data)) {
                $expenses_data_for_month[] = array('Day'=>"$show_date",'value'=>$expenses_data[$day], 'income' => isset($income_data[$day])?$income_data[$day]:0);
                $total_expense_of_month += $expenses_data[$day];
            } else {
                $expenses_data_for_month[] = array('Day'=>"$show_date",'value'=>0, 'income' => isset($income_data[$day])?$income_data[$day]:0);
            }
        }
        $expenses_grap_data = json_encode($expenses_data_for_month);
        
        return view('reports/monthly', [
            'expenses_grap_data' => $expenses_grap_data,
            'month' => $str_cur_month,
            'total_expense_of_month' => $total_expense_of_month,
            'month_numeric' => $current_month,
            'total_income_of_month' => $total_income_of_month,
            'income_data' => $income_data,
        ]);
    }
    /**
     * Get the income and expenses for the particular date
     *
     * @param Request $request
     * @return void
     */
    public function getDateIncome(Request $request, Expense $expense) {
        $date = $request->report_date;
        $income_qry = ReservationAdvance::select('category', 'mode_of_payment', 'paid', DB::raw('DATE(updated_at) as updated_at'))
                            ->whereRaw('DATE(updated_at) = "'.$date.'"');
        $result['income_list'] = $income_qry->get();
        //Works only with php 5.5 above
        $result['total_incomes'] = $income_qry->select(DB::raw('sum(paid) as amount'))->first()->amount;
        
        $result['expense_list'] = $this->expenses->allExpenses($expense, $date, $date);
        //Works only with php 5.5 above
        $result['total_expenses'] = array_sum(array_column($result['expense_list']->toArray(), 'amount'));
        
        return json_encode($result);
        
    }
    /**
     * Displays the datewise report of the expenses and income.
     *
     * @param Request $request
     * @return void
     */
    public function income(Request $request, Expense $expense) {
        $from_date = Date('Y-m-01', strtotime(Date('Y-m-d')));
        $to_date = Date('Y-m-d');
        
        if($request->isMethod('post')) {
            $from_date = Date('Y-m-d', strtotime($request->from_date));
            $to_date = Date('Y-m-d', strtotime($request->to_date));
        }
        $group_payments = DB::raw('(select sum(amount) as expense_amount, DATE(updated_at) as report_date from employee_payments where DATE(updated_at) >= "'.$from_date.'" and DATE(updated_at) <= "'.$to_date.'" group by DATE(updated_at)) a');
        
        $group_expenses = DB::raw('(select sum(amount) as expense_amount, DATE(date_of_expense) as report_date from expenses where DATE(date_of_expense) >= "'.$from_date.'" and DATE(date_of_expense) <= "'.$to_date.'" group by DATE(date_of_expense)) b');
        
        $group_reservation = DB::raw('(select sum(paid) as income_amount, DATE(updated_at) as report_date, sum(paid) as income from reservation_advances where DATE(updated_at) >= "'.$from_date.'" and DATE(updated_at) <= "'.$to_date.'" group by DATE(updated_at)) c');
        
        
        
        $first = DB::table($group_payments)->select('a.report_date', 'a.expense_amount as expense_amount', DB::raw('ifnull(c.income_amount,0) as income_amount'))
                    ->leftjoin($group_expenses, 'b.report_date', '=', 'a.report_date')
                    ->leftjoin($group_reservation, 'a.report_date', '=', 'c.report_date')
                    ->whereNull('b.report_date');

        $second = DB::table($group_reservation)->select('c.report_date', DB::raw('"0" as expense_amount'), 'c.income_amount as income_amount')
                    ->leftjoin($group_expenses, 'c.report_date', '=', 'b.report_date')
                    ->leftjoin($group_payments, 'c.report_date', '=', 'a.report_date')
                    ->whereNull('b.report_date')
                    ->whereNull('a.report_date');
                    
                    
        $incomes_qry = DB::table($group_expenses)->select('b.report_date', DB::raw('b.expense_amount + ifnull((select sum(employee_payments.amount) from employee_payments where DATE(employee_payments.updated_at) = DATE(b.report_date)),0) as expense_amount'), DB::raw('ifnull(c.income_amount,0) as income_amount'))
                    ->leftjoin($group_reservation, 'c.report_date', '=', 'b.report_date')
                    ->union($first)
                    ->union($second)
                    ->orderBy('report_date', 'desc');
                    
        $incomes = $incomes_qry->get();
        //print_r($incomes);die;
        /*income for datewise start*/
        $total_qry = DB::table(DB::raw('('.$incomes_qry->toSql().') as tot'))
                        ->addBinding($incomes_qry->getBindings())
                        ->select(DB::raw('ifnull (sum(tot.expense_amount),0) as amount'), DB::raw('ifnull (sum(tot.income_amount),0) as income_amount'))
                        ->first();
        $total_income = $total_qry->amount;
        $total_expenses = $total_qry->income_amount;
        
        /*income for datewise end*/
        
                    
        return view('reports/income', [
            'incomes' => $incomes,
            'from_date' => Date('d-m-Y', strtotime($from_date)),
            'to_date' => Date('d-m-Y', strtotime($to_date)),
            'total_income' => $total_income,
            'total_expenses' => $total_expenses,
        ]);         
    }
    
    /**
     * Determines the expense category based
     * on the route.
     *
     * @param String $route
     * @return String $exp_category
     */
    private function get_expense_category($route) {
        switch($route) {
            case 'food/add':
            case 'food/list':
                $exp_category = 'food';
            break;
            case 'laundry/add':
            case 'laundry/list':
                $exp_category = 'laundry';
            break;
            case 'electricity/add':
            case 'electricity/list':
                $exp_category = 'electricity';
            break;
            case 'housekeeping/add':
            case 'housekeeping/list':
                $exp_category = 'housekeeping';
            break;
            case 'internet/add':
            case 'internet/list':
                $exp_category = 'internet';
            break;
            case 'salary/add':
            case 'salary/list':
                $exp_category = 'salary';
            break;
            case 'bank/add':
            case 'bank/list':
                $exp_category = 'bank';
            break;
            case 'owner/add':
            case 'owner/list':
                $exp_category = 'owner';
            break;
            case 'advertisment/add':
            case 'advertisment/list':
                $exp_category = 'advertisment';
            break;
            case 'commission/add':
            case 'commission/list':
                $exp_category = 'commission';
            break;
            case 'tax/add':
            case 'tax/list':
                $exp_category = 'tax';
            break;
            default:
                $exp_category = 'others';
        }
        return $exp_category;
    }
}
