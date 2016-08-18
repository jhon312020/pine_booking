<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Customer extends Model
{
    protected $fillable = ['first_name', 'last_name','email','address','phone'];
    public function user() {
        return $this->belongsTo(User::class);
    }
}