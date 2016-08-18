<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class ReservationAdvance extends Model
{
    protected $fillable = ['reservation_id','paid'];
 
    function Reservation()
    {
        return $this->hasOne('App\Reservation');
    }
}
 