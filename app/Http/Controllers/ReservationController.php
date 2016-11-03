<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Session; 
use App\Reservation;
use App\ReservationNight;
use App\ReservationAdvance;
use App\Customer;
use App\Room;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
//        $this->role = \Auth::user()->role;
    }

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $reservation_date_from = date('Y-m-d', strtotime($request->from_date));
            $reservation_date_to = date('Y-m-d', strtotime($request->to_date));
        } else {
            $reservation_date_from = date('Y-m-d');
            $reservation_date_to = date('Y-m-t');
        }
        $reservations = Reservation::whereBetween('checkin',[$reservation_date_from, $reservation_date_to])->orderBy('created_at','DESC')->get();
        return view("reservation/index",[
                "reservations"=>$reservations,
                "reservation_date_from"=>date('d-m-Y', strtotime($reservation_date_from)),
                "reservation_date_to"=>date('d-m-Y', strtotime($reservation_date_to))
                ]);
    }

    public function advance($id, Request $request)
    {
        if ($request->isMethod('post')) {
            if($request->checkmyform == 'mypayment') {
            ReservationAdvance::insert(['paid' =>$request->paid, 'reservation_id'=>$id,'mode_of_payment'=>$request->mode_of_payment, 'category'=>$request->category, 'created_at'=>Carbon::now()->toDateTimeString(), 'updated_at'=>date('Y-m-d', strtotime($request->updated_at))]);
            Session::flash('alert-info', 'info');
            } else {
                $this->validate($request, [
                    'checkout' => 'required',
                    'total_price' => 'required',
                ]);
               
                if (Customer::where('phone', '=', $request->phone)->exists()) {
                    $mycustomer = Customer::where('phone', $request->phone)->first();
                    $customer_id = $mycustomer->id;
                } else {
                    $name = "blankimg.png";
                    if(isset($request->image) && !empty($request->image)) {
                        $file = Input::file('image');
                        $timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());
                        $name = $timestamp. '-' .$file->getClientOriginalName();
                        $file->move(public_path().'/images/customers/', $name);
                    }
                    $customer_id = Customer::insertGetId(['first_name' => $request->first_name, 'last_name' => $request->last_name, 'phone' => $request->phone, 'email' => $request->email, 'image'=> $name, 'address'=> $request->address ]);
                }
                Reservation::where('id',$request->id)->update(array(
                    'total_price' => $request->total_price, 'advance' =>$request->advance, 'booked_rooms' => $request->booked_rooms, 'checkin' => date('Y-m-d', strtotime($request->checkin)), 'checkout' => date('Y-m-d', strtotime($request->checkout)), 'customer_id' => $customer_id, 'reference' => $request->reference
                ));

                $dateDiff = abs(strtotime($request->checkout."-1 days") - strtotime($request->checkin));
                $number_of_reserved_days = $dateDiff/86400;
                ReservationNight::where('reservation_id',$request->id)->delete();
                for($i=0; $i<=$number_of_reserved_days; $i++) {
                    ReservationNight::insert(['day' => date('Y-m-d', strtotime($request->checkin."+ $i days")), 'booked_rooms' => $request->booked_rooms, 'reservation_id'=>$request->id,'created_at'=>Carbon::now()->toDateTimeString(), 'updated_at'=>Carbon::now()->toDateTimeString()]);
                }
                if ($request->has('advance')) {
                    ReservationAdvance::where('reservation_id',$request->id)->delete();
                    if($request->advance > 0) {
                        ReservationAdvance::insert(['paid' =>$request->advance, 'reservation_id'=>$request->id,'created_at'=>Carbon::now()->toDateTimeString(), 'updated_at'=>Carbon::now()->toDateTimeString()]);
                    }
                }
                Session::flash('alert-success', 'success');
            }
        }
        $reservation = Reservation::find($request->id);
        $reservation_advances = ReservationAdvance::where('reservation_id',$id)->get();
        $total_paid = ReservationAdvance::where('reservation_id',$id)->sum('paid');
        $total_available_rooms = Room::where('is_disabled', 0)->get();
        $minDateTo = date('Y-m-d', strtotime($reservation->checkin . ' +1 day'));
        return view("reservation/advance",["reservation"=>$reservation, "reservation_advances"=>$reservation_advances, 'total_paid'=> $total_paid, 'total_available_rooms' => $total_available_rooms, 'minDateTo'=>$minDateTo]);
    }
    
    public function add(Request $request)
    {
        $customers = ['' => 'Select Customer'] + Customer::lists("first_name","id")->all();
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'checkin' => 'required',
                'checkout' => 'required',
                'total_price' => 'required',
                'phone'=>'required',
                'first_name'=>'required',
                /*'last_name'=>'required',
                'email'=>'required',
                'address'=>'required'*/
            ]);
            if (Customer::where('phone', '=', $request->phone)->exists()) {
                $mycustomer = Customer::where('phone', $request->phone)->first();
                $customer_id = $mycustomer->id;
            } else {
                $name = "blankimg.png";
                if(isset($request->image) && !empty($request->image)) {
                    $file = Input::file('image');
                    $timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());
                    $name = $timestamp. '-' .$file->getClientOriginalName();
                    $file->move(public_path().'/images/customers/', $name);
                }
                $customer_id = Customer::insertGetId(['first_name' => $request->first_name, 'last_name' => $request->last_name, 'phone' => $request->phone, 'email' => $request->email, 'image'=> $name, 'address'=> $request->address ]);
            }
            $reservation_id = Reservation::insertGetId([
                    'total_price' => $request->total_price, 'advance' =>$request->advance, 'rent'=>$request->rent,'booked_rooms' => $request->booked_rooms, 'checkin' => date('Y-m-d', strtotime($request->checkin)), 'checkout' => date('Y-m-d', strtotime($request->checkout)), 'customer_id' => $customer_id, 'reference' => $request->reference, 'is_active' => (!is_null($request->is_active))? $request->is_active:0
                    ]); 

            $dateDiff = abs(strtotime($request->checkout."-1 days") - strtotime($request->checkin));
            $number_of_reserved_days = $dateDiff/86400;
            
            for($i=0; $i<=$number_of_reserved_days; $i++) {
                ReservationNight::insert(['day' => date('Y-m-d', strtotime($request->checkin."+ $i days")), 'booked_rooms' => $request->booked_rooms, 'reservation_id'=>$reservation_id,'created_at'=>Carbon::now()->toDateTimeString(), 'updated_at'=>Carbon::now()->toDateTimeString()]);
            }
            if ($request->has('advance')) {
                if($request->advance > 0) {
                    ReservationAdvance::insert(['paid' =>$request->advance, 'category' =>'Advance', 'mode_of_payment'=>'Cash', 'reservation_id'=>$reservation_id,'created_at'=>Carbon::now()->toDateTimeString(), 'updated_at'=>Carbon::now()->toDateTimeString()]);
                }
            }
            Session::flash('alert-success', 'success');
            return redirect('/reservation/index');
        } 
        $total_available_rooms = Room::where('is_disabled', 0)->get();
        $today = date('d-m-Y');
        $tomorrow = date("d-m-y", strtotime("+1 day"));
        $queries = array();
        $maxofdate = array();
        $orderbydate = ReservationNight::selectRaw('day, sum(booked_rooms) as sum')->groupBy('day')->whereBetween('day',array(date('Y-m-d'), date('Y-m-d',strtotime("+1 day"))))->lists('sum', 'day');
        foreach($orderbydate as $myorder) {
            $maxofdate[] = $myorder;
        }
        if(count($maxofdate)>0) {
            $queries = min($maxofdate);
        } else {
            $queries = 0;
        }
        return view("reservation/add",["customers" =>$customers, "total_available_rooms" => $total_available_rooms, 'today'=>$today, 'tomorrow'=>$tomorrow, 'queries' => $queries]);

    }
    public function view_detail(Request $request) {
        $reservation = Reservation::find($request->id);
        return view("reservation/view_detail",["reservation"=>$reservation]);
    }
    public function completed(Request $request)
    {
       Reservation::where('id',$request->id)->update(array('completed' => 1 ));
       ReservationNight::where('day','>=',date('Y-m-d'))->where('reservation_id',$request->id)->delete();
        Session::flash('flash_message_completed', 'Check out completed successfully!');
        return redirect('/reservation/index');
    }
    public function confirm(Request $request)
    {
       Reservation::where('id',$request->id)->update(array('is_active' => 1 ));
        Session::flash('flash_message_confirmed', 'Booking confirmed successfully!');
        return redirect('/reservation/index');
    }
    public function edit(Request $request)
    {
        $customers = ['' => 'Select Customer'] + Customer::lists("first_name","id")->all();
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'checkin' => 'required',
                'checkout' => 'required',
                'total_price' => 'required',
                'phone'=>'required',
                'first_name'=>'required',
                'last_name'=>'required',
                'email'=>'required',
                'address'=>'required'
            ]);
            if (Customer::where('phone', '=', $request->phone)->exists()) {
                $mycustomer = Customer::where('phone', $request->phone)->first();
                $customer_id = $mycustomer->id;
            } else {
                $name = "blankimg.png";
                if(isset($request->image) && !empty($request->image)) {
                    $file = Input::file('image');
                    $timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());
                    $name = $timestamp. '-' .$file->getClientOriginalName();
                    $file->move(public_path().'/images/customers/', $name);
                }
                $customer_id = Customer::insertGetId(['first_name' => $request->first_name, 'last_name' => $request->last_name, 'phone' => $request->phone, 'email' => $request->email, 'image'=> $name, 'address'=> $request->address ]);
            }
            Reservation::where('id',$request->id)->update(array(
                'total_price' => $request->total_price, 'advance' =>$request->advance, 'booked_rooms' => $request->booked_rooms, 'checkin' => date('Y-m-d', strtotime($request->checkin)), 'checkout' => date('Y-m-d', strtotime($request->checkout)), 'customer_id' => $customer_id, 'reference' => $request->reference
            ));

            $dateDiff = abs(strtotime($request->checkout."-1 days") - strtotime($request->checkin));
            $number_of_reserved_days = $dateDiff/86400;
            ReservationNight::where('reservation_id',$request->id)->delete();
            for($i=0; $i<=$number_of_reserved_days; $i++) {
                ReservationNight::insert(['day' => date('Y-m-d', strtotime($request->checkin."+ $i days")), 'booked_rooms' => $request->booked_rooms, 'reservation_id'=>$request->id,'created_at'=>Carbon::now()->toDateTimeString(), 'updated_at'=>Carbon::now()->toDateTimeString()]);
            }
            if ($request->has('advance')) {
                ReservationAdvance::where('reservation_id',$request->id)->delete();
                if($request->advance > 0) {
                    ReservationAdvance::insert(['paid' =>$request->advance, 'reservation_id'=>$request->id,'created_at'=>Carbon::now()->toDateTimeString(), 'updated_at'=>Carbon::now()->toDateTimeString()]);
                }
            }
            
            Session::flash('alert-success', 'success');
        } 
        $reservation = Reservation::find($request->id);
        return view("reservation/edit",["customers" =>$customers, "reservation"=>$reservation]);
    }
    public function delete($id)
    {
        Reservation::where('id',$id)->update(array('cancel' => 1 ));
        ReservationNight::where('reservation_id',$id)->delete();
        Session::flash('flash_message_cancel', 'Booking cancelled successfully!');
        return redirect('/reservation/index');
    }

    public function autocomplete(Request $request){
        $term = $request->term;
        $results = array();
        $queries = Customer::where('phone', 'LIKE', '%'.$term.'%')->take(5)->get();
        foreach ($queries as $query)
        {
            $results[] = [ 'id' => $query->id, 'value' => $query->phone ];
        }
        return response()->json($results);
    }

    public function show_customer_detail(Request $request) {
        $queries = Customer::where('id', $request->id)->get()->toArray();
        if (isset($queries[0]['image']) && $queries[0]['image']){
            $ext = pathinfo($queries[0]['image'], PATHINFO_EXTENSION);
            if (in_array($ext, array('png','jpg','jpeg','gif','bmp','tiff'))){
                $mime = 'image/'.$ext;
                $fileName = public_path().'/images/customers/'.$queries[0]['image'];
                if (file_exists($fileName)) {
                    $queries[0]['image'] = 'data:'.$mime.';base64,'.base64_encode(file_get_contents(public_path().'/images/customers/'.$queries[0]['image']));
                }
                else {
                    $queries[0]['image'] = '';
                }
            }
            else {
                $queries[0]['link'] = str_replace('/index.php/', '/', url('/images/customers')) . '/' . $queries[0]['image'];
                unset($queries[0]['image']);
            }
        }
        return response()->json($queries);
    }

    public function show_available_room_list(Request $request) {
        $queries = array();
        $no_of_reserved_days = array();
        $get_filled_list = array();
        $dateDiff = abs(strtotime($request->checkout."-1 days") - strtotime($request->checkin));
        $number_of_reserved_days = $dateDiff/86400;
        for($i=0; $i<=$number_of_reserved_days; $i++) {
            $no_of_reserved_days[date('Y-m-d', strtotime($request->checkin."+ $i days"))] = date('Y-m-d', strtotime($request->checkin."+ $i days"));
        }
        $find_reserved_rooms = ReservationNight::whereIn('day',$no_of_reserved_days)->get();
        foreach ($find_reserved_rooms as $key => $find_reserved_room) {
            $get_filled_list[] = $find_reserved_room->room_type_id;
        }
        $find_available_rooms = Room::whereNotIn('id',$get_filled_list)->get();

        foreach ($find_available_rooms as $key => $find_available_room) {
            $queries[$find_available_room->id]=$find_available_room->room_number;
        }
        return response()->json($queries);
    }

    public function check_available_rooms(Request $request) {
        $queries = array();
        $maxofdate = array();
        $orderbydate = ReservationNight::selectRaw('day, sum(booked_rooms) as sum')->groupBy('day')->whereBetween('day',array(date('Y-m-d', strtotime($request->checkin)), date('Y-m-d',strtotime($request->checkout."-1 days"))))->lists('sum', 'day');
        foreach($orderbydate as $myorder) {
            $maxofdate[] = $myorder;
        }
        if(count($maxofdate)>0) {
            $queries = min($maxofdate);
        } else {
            $queries = 0;
        }
        return response()->json($queries);
    }

}
