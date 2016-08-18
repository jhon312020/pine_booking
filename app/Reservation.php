<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Reservation extends Model
{
    protected $fillable = ['total_price', 'advance', 'checkin', 'checkout', 'customer_id', 'room_type_id', 'booked_rooms', 'reference'];
    public function room_type()
    {
        return $this->belongsTo('App\Room');
    }
    function Customer(){
        return $this->belongsTo('App\Customer');
    }
	public function user() {
        return $this->belongsTo(User::class);
    }
}