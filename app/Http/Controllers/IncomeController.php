<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use App\Http\Requests;
use App\Income;
use App\ReservationAdvance;
use DB;
use Auth;
use App\Http\Controllers\Controller;
use App\Repositories\IncomeRepository;

class IncomeController extends Controller
{
    /**
     * The income repository instance.
     * 
     * @var incomeRespository 
     */
    protected $incomes;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(IncomeRepository $incomes)
    {
        $this->middleware('auth');
		if(isset(\Auth::user()->role))
			$this->incomes = $incomes;
    }
    
    /**
     * Displays all the incomes of the logged in user.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
         if ($request->isMethod('post')) {
            $income_date_from = date('Y-m-d', strtotime($request->date_of_income_from));
            $income_date_to = date('Y-m-d', strtotime($request->date_of_income_to));
            $income_date_from_view = date('d-m-Y', strtotime($request->date_of_income_from));
            $income_date_to_view = date('d-m-Y', strtotime($request->date_of_income_to));
        } else {
            $income_date_to = $income_date_from = date('Y-m-d');
            $income_date_to_view = $income_date_from_view = date('d-m-Y');
        }
        return view('incomes/list',[
            'incomes' => $this->incomes->forUser($request->user(),$income_date_from, $income_date_to ),
            'page_title' => 'All incomes',
            'income_date_to' => $income_date_to_view,
            'income_date_from' => $income_date_from_view
        ]);
    }
    
    /**
     * Allows the logged in user to create new income.
     * Works for both GET and POST Methods
     * 
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'name' => 'required | max:100',
                'date_of_income' => 'required',
                'amount' => 'required'
            ]);
            
            $request->user()->incomes()->create([
                'name' => $request->name,
                'date_of_income' => date('Y-m-d', strtotime($request->date_of_income)),
                'amount' => $request->amount,
                'notes' => $request->notes
            ]);
        } 
        return view('incomes/add');
    }
    
    /**
     * Allows the logged in user to delete his income.
     *
     * @param Request $request
     * @param income $income
     * @return void
     */
    public function delete(Request $request, income $income)
    {
        if ($this->authorize('destroy', $income)) {
            $income_date = $income->select('date_of_income')->where('id', $income->id)->get();
            $request->session()->put('date_of_income', $income_date[0]->date_of_income);
            $income->delete();
        }
        return redirect('/income/list');
    }
   /**
     * Lists all the expenses of the compay.
     *
     * @param Request $request
     * @return Response
     */
    public function listing(Request $request, income $income)
    {
		$from_date = $to_date = Date('Y-m-d');
        if ($request->isMethod('post')) {
            $from_date = date('Y-m-d', strtotime($request->from_date));
            $to_date = date('Y-m-d', strtotime($request->to_date));
        }
        $income_qry = ReservationAdvance::select('category', 'mode_of_payment', 'paid', DB::raw('DATE(updated_at) as updated_at'))
							->whereRaw('DATE(updated_at) >= "'.$from_date.'"')
							->whereRaw('DATE(updated_at) <= "'.$to_date.'"');
		$income_list = $income_qry->get();
        //Works only with php 5.5 above
        $total_incomes = $income_qry->select(DB::raw('sum(paid) as amount'))->first()->amount;
		
        return view('incomes/'.Auth::User()->role.'/listing',[
            'income_list' => $income_list,
          //  'category' => $exp_category,
            'from_date' => date('d-m-Y', strtotime($from_date)),
            'to_date' => date('d-m-Y', strtotime($to_date)),
            'total_incomes' => $total_incomes,
        ]);
    }
}