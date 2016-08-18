<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    /**
     * Get the taks for the user
     * 
     */
    public function expenses() {
        return $this->hasMany(Expense::class);
    }
    /**
     * Get the taks for the user
     * 
     */
    public function incomes() {
        return $this->hasMany(Income::class);
    }
    /**
     * Get the taks for the user
     * 
     */
    public function rooms() {
        return $this->hasMany(Room::class);
    }
    /**
     * Get the taks for the user
     * 
     */
    public function reservations() {
        return $this->hasMany(Reservation::class);
    }
       
}
