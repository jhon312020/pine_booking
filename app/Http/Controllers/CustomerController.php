<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Session;
use App\Http\Requests;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Repositories\RoomRepository;
use Carbon\Carbon;

class CustomerController extends Controller
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
     * Lists all the Customers of the compay.
     *
     * @param Request $request
     * @return Response
     */
    public function listing()
    {
        $customers = Customer::where('is_active', 1)->get();
        return view('customers/'.$this->role.'/listing',['customers'=>$customers]);
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
                'email' => 'required'
            ]);
			$image_name = null;
            if(isset($request->image) && !empty($request->image)) {
				
				$file = Input::file('image');
				$timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());
				$image_name = $timestamp. '-' .$file->getClientOriginalName();
				$file->move(public_path().'/images/customers/', $image_name);
			}
            //$customer=new Customer($request->all());
            //$customer->save();
			Customer::insert(['first_name' => $request->first_name, 'last_name' => $request->last_name, 'phone' => $request->phone, 'email' => $request->email, 'image'=> $image_name, 'address'=> $request->address ]);
            Session::flash('alert-success', 'success');
        } 
        return view('customers/add');
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
        $customer = Customer::find($id);
        $customer::where('id',$id)->update(array('is_active' => 0));
        Session::flash('alert-danger', 'danger');
        return redirect('/customer/list');
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
                'email' => 'required'
            ]);
			$data = array(
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
            );
			$image_name = null;
            if(isset($request->image) && !empty($request->image)) {
				$file = Input::file('image');
				$timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());
				$image_name = $timestamp. '-' .$file->getClientOriginalName();
				$file->move(public_path().'/images/customers/', $image_name);
				$data['image'] = $image_name;
			}
            Customer::where('id',$request->id)->update($data);
            Session::flash('alert-success', 'success');
        } 
        $customer = Customer::find($request->id);
        return view('customers/edit', ['customer' => $customer]);
    }

    
}
