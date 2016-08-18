<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Employee extends Model
{
    protected $fillable = ['first_name', 'last_name','email','address','phone', 'salary'];
    public function user() {
        return $this->belongsTo(User::class);
    }
}