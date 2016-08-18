<?php	

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Session;
use App\Http\Requests;
use App\Employee;
use Validator;
use DB;
use App\EmployeePayment;
use App\Http\Controllers\Controller;
use App\Repositories\RoomRepository;
use Carbon\Carbon;

class EmployeeController extends Controller
{
   
    protected $role;
   
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RoomRepository $rooms)
    {
        $this->middleware('auth');
		if(isset(\Auth::user()->role))
			$this->role = \Auth::user()->role;
    }
    /**
     * Lists all the Employees of the compay.
     *
     * @param Request $request
     * @return Response
     */
    public function listing()
    {
        $Employees = Employee::where('is_active', 1)->get();
        return view('employees/'.$this->role.'/listing',['Employees'=>$Employees]);
    }
    
    public function pay($id, Request $request)
    {
        $category = array('advance' => 'Advance', 'salary' => 'Salary');
        $mymonth = date('m');
        $myyear = date('Y');
		$rule = array('paid' => 'required|numeric', 'category' => 'required', 'updated_at' => 'required|date_format:d-m-Y');
        if ($request->isMethod('post')) {
				if($request->category == 'salary') {
					$month = Date('m', strtotime('-1 month', strtotime($request->updated_at)));
					unset($rule['paid']);
					$salary = EmployeePayment::select('paid')
									->where('category', DB::raw("'salary'"))
									->whereRaw('MONTH(updated_at) = "'.$month.'"')
									->where('employee_id', $id)->count();
					if($salary == 0) {
					 $paid_amount = Employee::select(DB::raw('(employees.salary - ifnull((select sum(employee_payments.amount) as paid from employee_payments where employee_payments.employee_id = employees.id and employee_payments.category = "advance" and MONTH(employee_payments.updated_at) = "'.$month.'"), 0)) as balance'))
						->where('employees.id', $id)->first()->balance;
						if($paid_amount == 0)
							$v->errors()->add('paid', 'There is no pending payments');
						else
							$request->paid = $paid_amount;
					}
				}
				$v = Validator::make($request->all(), $rule);
			
				$v->after(function($v) use($id, $request) {
					$month = Date('m', strtotime($request->updated_at));
					$salary = EmployeePayment::select('paid')
								->where('category', DB::raw("'salary'"))
								->whereRaw('MONTH(updated_at) = "'.$month.'"')
								->where('employee_id', $id)->count();
					if($request->category == 'advance') {
						$advance = Employee::select(DB::raw('(employees.salary - ifnull((select sum(employee_payments.amount) as paid from employee_payments where employee_payments.employee_id = employees.id and employee_payments.category = "advance" and MONTH(employee_payments.updated_at) = "'.$month.'"), 0)) as balance'), 'employees.salary')
								->where('employees.id', $id)->first();
								
						if ($salary > 0) {
							$v->errors()->add('paid', 'There is no pending payments');
						} else if($advance && $advance->balance == 0) {
							$v->errors()->add('paid', 'There is no pending advance');
						} else if($advance && $request->paid > $advance->balance) {
							$v->errors()->add('paid', 'Advance should be less than or equal to '.$advance->balance);
						}
					} else if($request->category == 'salary') {
						$prev_month = Date('m', strtotime('-1 month', strtotime($request->updated_at)));
						$prev_salary = EmployeePayment::select('paid')
								->where('category', DB::raw("'salary'"))
								->whereRaw('MONTH(updated_at) = "'.$prev_month.'"')
								->where('employee_id', $id)->count();
						if($salary > 0) {
							$v->errors()->add('paid', 'This employee already received salary!');
						}
					}
				});
			
			if ($v->fails()) {
				return redirect('employees/pay/'.$id)
							->withErrors($v)
							->withInput();
			}
            if($request->has('advance_report_month')) {
                $date      = explode('/', $request->advance_report_month); 
                $mymonth      = $date[0];
                $myyear     = $date[1];
            } else {
                EmployeePayment::insert(['amount' =>$request->paid, 'category'=>$request->category,'employee_id'=>$id,'created_at'=>Carbon::now()->toDateTimeString(), 'updated_at'=>date('Y-m-d', strtotime('-1 month', strtotime($request->updated_at)))]);
                Session::flash('alert-success', 'success');
            }
        }

        $employees = Employee::where('id',$id)->get();
        $employee_payments = EmployeePayment::where('employee_id',$id)->whereMonth('updated_at','=',$mymonth)->whereYear('updated_at','=',$myyear)->orderBy('updated_at', 'DESC')->get();
        $total_advance_paid = EmployeePayment::where('employee_id',$id)->whereMonth('updated_at','=',$mymonth)->whereYear('updated_at','=',$myyear)->where('category',$category)->sum('amount');
        return view("employees/pay",["employees"=>$employees, "employee_payments"=>$employee_payments, 'total_advance_paid'=> $total_advance_paid, 'category' => $category]);
    }
	/**
     * Get salary info for the employee
     *
     * @param Request $request
     * @return JSON
     */
    public function getSalary(Request $request)
    {
		$month = Date('m', strtotime('-1 month', strtotime($request->pay_date)));
		$is_salary = EmployeePayment::select('paid')
								->where('category', DB::raw("'salary'"))
								->whereRaw('MONTH(updated_at) = "'.$month.'"')
								->where('employee_id', $request->id)->count();
		$salary = array();
		if($is_salary == 0) {
		$salary_amount = Employee::select(DB::raw('(employees.salary - ifnull((select sum(employee_payments.amount) as paid from employee_payments where employee_payments.employee_id = employees.id and employee_payments.category = "advance" and MONTH(employee_payments.updated_at) = "'.$month.'"), 0)) as balance'))
						->where('employees.id', $request->id)->first();
						
			return $salary_amount->toJSON();
		} else {
			return json_encode($salary); 
		}
		
	}
    /**
     * Allows the user to create a new room.
     * Works for both GET and POST Methods
     * 
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'address' => 'required',
                'phone' => 'required',
                'email' => 'required',
				'salary' => 'required'
            ]);
			$image_name = null;
            if(isset($request->image) && !empty($request->image)) {
				$file = Input::file('image');
				$timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());
				$image_name = $timestamp. '-' .$file->getClientOriginalName();
				$file->move(public_path().'/images/employee/', $image_name);
			}
            //$Employee=new Employee($request->all());
            //$Employee->save();
			Employee::insert(['first_name' => $request->first_name, 'last_name' => $request->last_name, 'phone' => $request->phone, 'email' => $request->email, 'image'=> $image_name, 'address'=> $request->address, 'salary' => $request->salary ]);
            Session::flash('alert-success', 'success');
        } 
        return view('employees/add');
    }
    
    /**
     * Allows the logged in user to delete his room.
     *
     * @param Request $request
     * @param room $room
     * @return void
     */
    public function delete($id)
    {
        $Employee = Employee::find($id);
        Employee::where('id',$id)->update(['is_active' => 0]);
        Session::flash('alert-danger', 'danger');
        return redirect('/employees/list');
    }
    
    /**
     * Allows the user to create a new room.
     * Works for both GET and POST Methods
     * 
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
               'first_name' => 'required',
                'last_name' => 'required',
                'address' => 'required',
                'phone' => 'required',
                'email' => 'required',
				'salary' => 'required'
            ]);
            $image_name = null;
			$data = array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
				'salary' => $request->salary,
            );
            if(isset($request->image) && !empty($request->image)) {
				
				$file = Input::file('image');
				$timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());
				$image_name = $timestamp. '-' .$file->getClientOriginalName();
				$file->move(public_path().'/images/employee/', $image_name);
				$data['image'] = $image_name;
			}
            Employee::where('id',$request->id)->update($data);
            Session::flash('alert-success', 'success');
        } 
        $Employee = Employee::find($request->id);
        return view('employees/edit', ['Employee' => $Employee]);
    }
}
