<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class EmployeePayment extends Model
{
    protected $fillable = ['category','amount'];
 
    function Employee()
    {
        return $this->hasOne('App\Employee');
    }
}