<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Session;
use App\Http\Requests;
use App\Room;
use App\Reservation;
use App\RoomType;
use App\Http\Controllers\Controller;
use App\Repositories\RoomRepository;

class RoomController extends Controller
{
   
    protected $rooms;
    protected $role;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RoomRepository $rooms)
    {
        $this->middleware('auth');
        $this->rooms = $rooms;
		if(isset(\Auth::user()->role))
			$this->role = \Auth::user()->role;
    }
    /**
     * Displays all the rooms of the company.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, Room $room)
    {
        $room_object = $this->rooms->allRooms($room);
        //Works only with php 5.5 above
        $total_rooms = array_sum(array_column($room_object->toArray(), 'amount'));
        return view('rooms/'.$this->role.'/index',[
            'rooms' => $room_object,
            'page_title' => 'OverAll Rooms',
            'total_rooms' => number_format($total_rooms, 2)
        ]);
        
    }
   
    /**
     * Lists all the rooms of the compay.
     *
     * @param Request $request
     * @return Response
     */
    public function listing()
    {
        $rooms = Room::all();
        return view('rooms/'.$this->role.'/listing',['rooms'=>$rooms]);
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
                'room_number' => 'required'
            ]);
            $request->user()->rooms()->create([
                'room_number' => $request->room_number,
            ]);
            Session::flash('alert-success', 'success');
            Session::flash('added_room',$request->room_number);
        } 
        return view('rooms/add');
    }
    
    /**
     * Allows the logged in user to delete his room.
     *
     * @param Request $request
     * @param room $room
     * @return void
     */
    public function delete(Request $request, Room $room)
    {
        $room = Room::find($room->id);
        $room->delete();
        Session::flash('alert-danger', 'danger');
        return redirect('/room/list');
    }
	/**
     * Allows the logged in user to enable or disable his room.
     *
     * @param $id
     * @param room $room
     * @return void
     */
    public function update($id, Room $room)
    {
        $room = Room::find($id);
		//$reservations = Reservation::whereRaw("booked_rooms = '".$id."' and DATE('".Date('Y-m-d')."') between DATE(checkin) and DATE_SUB(checkout,INTERVAL 1 DAY)")->count();
		$reservations = 0;
		if($reservations == 0) {
			if($room->is_disabled == 0) {
				Room::where('id',$id)->update(array(
					'is_disabled' => 1,
				));
				$text = 'disabled';
			} else {
				Room::where('id',$id)->update(array(
					'is_disabled' => 0,
				));
				$text = 'enabled';
			}
			Session::flash('alert-success', "Room $text successfully!");
		} else {
			Session::flash('alert-success', "You are not allowd to disabled this roo because this room is already booked");
		}
        
        //Session::flash('added_room',$request->room_number);
        return redirect('/room/list');
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
        $exp_category = 'room';
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'room_number' => 'required'
            ]);
            
            Room::where('id',$request->id)->update(array(
                'room_number' => $request->room_number,
            ));
            Session::flash('alert-success', 'success');
            Session::flash('added_room',$request->room_number);
        } 
        $room = Room::find($request->id);
        return view('rooms/edit', ['room' => $room]);
    }

    
    /**
     * Determines the room category based
     * on the route.
     *
     * @param String $route
     * @return String $exp_category
     */
    private function get_room_category($route) {
        switch($route) {
            case 'room/add':
            case 'room/list':
            case 'room/edit':
                $exp_category = 'room';
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
            default:
                $exp_category = 'others';
        }
        return $exp_category;
    }
}
