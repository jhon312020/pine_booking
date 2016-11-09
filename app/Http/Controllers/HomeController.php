<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use App\Expense;
use App\Room;
use App\Income;
use App\Reservation;
use App\ReservationAdvance;
use App\ReservationNight;
use Illuminate\Http\Request;
use DB;

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
        if(isset(\Auth::user()->role))
            $this->role = \Auth::user()->role;
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
        if ($request->isMethod('post')) {
            $room_availability_from = date('Y-m-d', strtotime($request->room_availability_from));
            $room_availability_to = date('Y-m-d', strtotime($room_availability_from .' +14 days'));
            $month_lead = date("n", strtotime($request->month));
        } else {
            $room_availability_from= date('Y-m-d');
            $room_availability_to = date('Y-m-d', strtotime($room_availability_from .' +14 days'));
            $month_lead = date("n");
        }
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime("-1 days"));
        $month = date('m');
        
        $expenses_grouped_for_user = $this->expenses->expensesOfCompanyGroupedByDate($expense, $month);
        //$expenses_grouped = $expenses_grouped_for_user->toArray();
        $expenses_data = array_combine(array_column($expenses_grouped_for_user, 'report_date'), array_column($expenses_grouped_for_user, 'totalamount'));
        $last_day_this_month  = date('t');
		$total_expense_of_month = 0;
        
        /*income for month start*/
        $expense_count = $expense->whereRaw('MONTH(date_of_expense) = "'.$month.'" and YEAR(date_of_expense) = YEAR(NOW())')->count() + DB::table('employee_payments')->whereRaw('MONTH(updated_at) = "'.$month.'" and YEAR(updated_at) = YEAR(NOW())')->count();
		
        $income_count = Income::select('id')->whereRaw('MONTH(updated_at) = "'.$month.'" and YEAR(updated_at) = YEAR(NOW())')->count() + 
							ReservationAdvance::select('id')->whereRaw('MONTH(updated_at) = "'.$month.'" and YEAR(updated_at) = YEAR(NOW())')
							->count();
		
        
        $subQry = Income::select(DB::raw('sum(amount) as amount'), DB::raw('DAY(updated_at) as paid_date'))
				->whereRaw('MONTH(updated_at) = "'.$month.'" and YEAR(updated_at) = YEAR(NOW())')
				->groupby(DB::raw('DATE(updated_at)'))
				->union(ReservationAdvance::select(DB::raw('sum(paid) as amount'), DB::raw('DAY(updated_at) as paid_date'))
				->whereRaw('MONTH(updated_at) = "'.$month.'" and YEAR(updated_at) = YEAR(NOW())'))
				->groupby(DB::raw('DATE(updated_at)'));
		
		$data = DB::table( DB::raw("({$subQry->toSql()}) as sub") )
				->mergeBindings($subQry->getQuery())
				->select(DB::raw('sum(sub.amount) as amount'), 'sub.paid_date')
				->groupby('paid_date')
				->get();
				
		
		$income = json_decode(json_encode($data), true);
        $income_data = array_combine(array_column($income, 'paid_date'), array_column($income, 'amount'));
		
        $total_income_of_month = 0;
        $total_income = DB::table( DB::raw("({$subQry->toSql()}) as sub") )
						->mergeBindings($subQry->getQuery())
						->select(DB::raw('sum(sub.amount) as amount'))
						->first();
        if($total_income->amount)
            $total_income_of_month = $total_income->amount;
        /*income for month end*/
        
        for ($day = 1; $day <= $last_day_this_month; $day++) {
            $process_date = date("Y-m-$day");
            $show_date = date('d-m-Y', strtotime($process_date));
            if (array_key_exists($day, $expenses_data)) {
                $expenses_data_for_month[] = array('Day'=>"$show_date",'value'=>$expenses_data[$day], 'income' => isset($income_data[$day])?$income_data[$day]:0);
                $total_expense_of_month += $expenses_data[$day];
            } else {
                $expenses_data_for_month[] = array('Day'=>"$show_date",'value'=>0, 'income' => isset($income_data[$day])?$income_data[$day]:0);
            }
        }
        $no_of_reserved_days[]=array();
        $reserved_rooms = array();
        foreach ($reserved_rooms as $key => $reserved_room) {
            $no_of_reserved_days[$reserved_room->room_type_id][] = date('Y-m-d', strtotime($reserved_room->day));
        }
        $rooms = Room::where('is_disabled', 0)->get();
        //DB::enableQueryLog();
        $orderbydate = ReservationNight::selectRaw('day, sum(booked_rooms) as sum')->groupBy('day')->where('day', '>=', $room_availability_from)->where('day', '<=', $room_availability_to)->where('is_active', 0)->lists('sum', 'day');
        //dd(DB::getQueryLog());
        //~ echo '<pre>';
        //~ print_r($orderbydate);
        //~ echo '</pre>';
        $expenses_grap_data = json_encode($expenses_data_for_month);
        return view('home', [
            'expenses_grap_data' => $expenses_grap_data,
            'total_expense_of_month' => $total_expense_of_month,
            'total_income_of_month' => $total_income_of_month,
            'reserved_rooms' => $reserved_rooms,
            'rooms' => $rooms,
            'number_of_days' => 15,
            'room_availability_from' => $room_availability_from,
            'no_of_reserved_days' => $no_of_reserved_days,
            'orderbydate' => $orderbydate,
            'expense_count' => $expense_count,
            'income_count' => $income_count,
            'role'=>$this->role
        ]);
    }
}
