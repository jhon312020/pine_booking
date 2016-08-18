<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class ReservationNight extends Model
{
	
    protected $fillable = ['rate', 'date', 'room_type_id','reservation_id', 'booked_rooms'];
 
    function Reservation()
    {
        return $this->hasOne('App\Reservation');
    }
}
 