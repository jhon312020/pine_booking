<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Http\Requests;
use App\Expense;
use App\Income;
use App\ReservationAdvance;
use App\EmployeePayment;
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
     * @var food category
     */
    protected $food_category = array('tea' => 'Tea', 'snacks' => 'Snacks');
    
    /*
     * 
     * @var expense_category
     * 
     */
    protected $expense_category = array("Food"=>"Food", "Electricity"=>"Electricity", "Electricity Maintenance"=>"Electricity Maintenance", "Laundry"=>"Laundry", "Internet"=>"Internet", "Material"=>"Material", "Building"=>"Building", "House Keeping"=>"House Keeping", "Bank"=>"Bank", "Owner"=>"Owner", "Advertisement"=>"Advertisement", "Commission"=>"Commission", "Tax"=>"Tax", "Labour/Salary"=>"Labour/Salary", "Other Labour"=>"Other Labour", "Others"=>"Others");
    
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
        return view('expenses/add', ['expenses' => $expense_object, 'food_category' => $this->food_category, 'expense_category' => $this->expense_category]);
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
     * Allows the logged in user to delete his expense.
     *
     * @param Request $request
     * @param Expense $expense
     * @return void
     */
    public function edit(Request $request, Expense $expense)
    {
        $expense = Expense::find($request->id);
        $today = date('Y-m-d');
        $expense_created_date = date('Y-m-d', strtotime($expense->created_at));
        if ($expense_created_date == $today) {
            if ($request->isMethod('post')) {
                $this->validate($request, [
                    'name' => 'required | max:100',
                    'date_of_expense' => 'required',
                    'amount' => 'required'
                ]);
                $expense->name = $request->name;
                $expense->date_of_expense = date('Y-m-d', strtotime($request->date_of_expense));
                $expense->category = $request->category;
                $expense->amount = $request->amount;
                $expense->notes= $request->notes;
                $expense->save();
                return redirect('/expense/add');
            }
            return view('expenses/edit', ['expense_category' => $this->expense_category, 'food_category' => $this->food_category, 'expense'=>$expense]);
        } else {
            return redirect('/expense/add');
        }
        
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
    if ($this->role != 'admin') {
            return redirect('/home');
        }
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
     $subQry = Income::select(DB::raw('sum(amount) as amount'), DB::raw('MONTH(date_of_income) as paid_month'))
        ->whereRaw('YEAR(date_of_income) = "'.$year.'"')
        ->groupby(DB::raw('MONTH(date_of_income)'))
        ->unionAll(ReservationAdvance::select(DB::raw('sum(paid) as amount'), DB::raw('MONTH(updated_at) as paid_month'))
        ->whereRaw('YEAR(updated_at) = "'.$year.'"')
        ->groupby(DB::raw('MONTH(updated_at)')));
    
    $data = DB::table( DB::raw("({$subQry->toSql()}) as sub") )
        ->mergeBindings($subQry->getQuery())
        ->select(DB::raw('sum(sub.amount) as amount'), 'sub.paid_month')
        ->groupby('paid_month')
        ->get();
    
    $income = json_decode(json_encode($data), true);
        $income_data = array_combine(array_column($income, 'paid_month'), array_column($income, 'amount'));
     
     
    $total_income = DB::table( DB::raw("({$subQry->toSql()}) as sub") )
            ->mergeBindings($subQry->getQuery())
            ->select(DB::raw('sum(sub.amount) as amount'))
            ->first();
        if($total_income->amount)
            $total_income_of_year = $total_income->amount;
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
            $last_day_this_month  = date('t');
            $month = date("m");
            $month_lead = date("n");
        }
        $current_month = date("m");
        
        $expenses_grouped = $this->expenses->expensesOfCompanyGroupedByDate($expense, $month);
        //$expenses_grouped = $expenses_grouped_for_user->toArray();
        $expenses_data = array_combine(array_column($expenses_grouped, 'report_date'), array_column($expenses_grouped, 'totalamount'));
        
        /*income for month start*/
        $subQry = Income::select(DB::raw('sum(amount) as amount'), DB::raw('DAY(date_of_income) as paid_date'))
        ->whereRaw('MONTH(date_of_income) = "'.$month_lead.'" and YEAR(date_of_income) = YEAR(NOW())')
        ->groupby(DB::raw('DATE(date_of_income)'))
        ->unionAll(ReservationAdvance::select(DB::raw('sum(paid) as amount'), DB::raw('DAY(updated_at) as paid_date'))
        ->whereRaw('MONTH(updated_at) = "'.$month_lead.'" and YEAR(updated_at) = YEAR(NOW())'))
        ->groupby(DB::raw('DATE(updated_at)'));
    
    $data = DB::table( DB::raw("({$subQry->toSql()}) as sub") )
        ->mergeBindings($subQry->getQuery())
        ->select(DB::raw('sum(sub.amount) as amount'), 'sub.paid_date')
        ->groupby('paid_date')
        ->get();
        
    
    $income = json_decode(json_encode($data), true);
        $income_data = array_combine(array_column($income, 'paid_date'), array_column($income, 'amount'));
        
        $total_income_of_month = $this->incomes->incomeOfUserForMonth($request->user(), $month, date('Y'))->toArray();
        if ($total_income_of_month) {
            $total_income_of_month = $total_income_of_month[0]['totalamount'];
        } else {
            $total_income_of_month = 0;
        }
        
        $total_income = DB::table( DB::raw("({$subQry->toSql()}) as sub") )
            ->mergeBindings($subQry->getQuery())
            ->select(DB::raw('sum(sub.amount) as amount'))
            ->first();
    if($total_income->amount)
            $total_income_of_month = $total_income->amount;       
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
    $income_qry = Income::select(DB::raw('"income" as category'), 'name as mode_of_payment', 'amount as paid')
        ->whereRaw('DATE(date_of_income) = "'.$date.'"')
        ->unionAll(ReservationAdvance::select('category', 'mode_of_payment', 'paid')
        ->whereRaw('DATE(updated_at) = "'.$date.'"'));
        
        $result['income_list'] = $income_qry->get();
        //Works only with php 5.5 above
        $result['total_incomes'] = DB::table( DB::raw("({$income_qry->toSql()}) as sub") )
                ->mergeBindings($income_qry->getQuery())
                ->select(DB::raw('sum(sub.paid) as amount'))
                ->first()->amount;
        
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
        
          
    $subQry = Income::select(DB::raw('sum(amount) as amount'), DB::raw('0 as expense_amount'), DB::raw('DATE(date_of_income) as report_date'))
        ->whereRaw('DATE(date_of_income) >= "'.$from_date.'" and DATE(date_of_income) <= "'.$to_date.'"')
        ->groupby(DB::raw('DATE(date_of_income)'))
        ->unionAll(ReservationAdvance::select(DB::raw('sum(paid) as amount'), DB::raw('0 as expense_amount'), DB::raw('DATE(updated_at) as report_date'))
        ->whereRaw('DATE(updated_at) >= "'.$from_date.'" and DATE(updated_at) <= "'.$to_date.'"')
        ->groupby(DB::raw('DATE(updated_at)')))
        ->unionAll(Expense::select(DB::raw('0 as amount'), DB::raw('sum(amount) as expense_amount'), DB::raw('DATE(date_of_expense) as report_date'))
        ->whereRaw('DATE(date_of_expense) >= "'.$from_date.'" and DATE(date_of_expense) <= "'.$to_date.'"')
        ->groupby(DB::raw('DATE(date_of_expense)')))
        ->unionAll(EmployeePayment::select(DB::raw('0 as amount'), DB::raw('sum(amount) as expense_amount'), DB::raw('DATE(updated_at) as report_date'))
        ->whereRaw('DATE(updated_at) >= "'.$from_date.'" and DATE(updated_at) <= "'.$to_date.'"')
        ->groupby(DB::raw('DATE(updated_at)')));
    
    $incomes = DB::table( DB::raw("({$subQry->toSql()}) as sub") )
        ->mergeBindings($subQry->getQuery())
        ->select(DB::raw('sum(sub.amount) as income_amount'), DB::raw('sum(sub.expense_amount) as expense_amount'), 'sub.report_date')
        ->groupby('report_date')
        ->get();    

        //$incomes = $incomes_qry->get();
        //print_r($incomes);die;
        /*income for datewise start*/
        $total_qry = DB::table(DB::raw('('.$subQry->toSql().') as tot'))
                        ->addBinding($subQry->getBindings())
                        ->select(DB::raw('ifnull (sum(tot.expense_amount),0) as expense_amount'), DB::raw('ifnull (sum(tot.amount),0) as income_amount'))
                        ->first();

        $total_income = $total_qry->income_amount;
        $total_expenses = $total_qry->expense_amount;        
        /*income for datewise end*/
        
                    
        return view('reports/income', [
            'incomes' => $incomes,
            'from_date' => Date('d-m-Y', strtotime($from_date)),
            'to_date' => Date('d-m-Y', strtotime($to_date)),
            'total_income' => $total_income,
            'total_expenses' => $total_expenses,
        ]);         
    }
}
