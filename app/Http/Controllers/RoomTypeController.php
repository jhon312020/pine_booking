<?php
 
namespace App\Http\Controllers;
 
use App\RoomType;
use Illuminate\Http\Request;
use Session; 
use App\Http\Requests;
use App\Http\Controllers\Controller;
 
 
 
class RoomTypeController extends Controller
{
 
    public function index()
    {
        $room_types = RoomType::all();
        return view("room_type/index",["room_types"=>$room_types]);
    }
 
    public function store(Request $request)
    {
        $room_type=new RoomType($request->all());
        $room_type->save();
        return $room_type;
    }
    public function delete($id)
    {
        $room_type = RoomType::findOrFail($id);
        $room_type->delete();
        Session::flash('flash_message', 'Task successfully deleted!');
    }
}