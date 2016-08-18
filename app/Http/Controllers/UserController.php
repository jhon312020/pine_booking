<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Hash;

use App\Http\Requests;
use App\Expense;
use App\Income;
use App\User;

use App\Http\Controllers\Controller;
use App\Repositories\ExpenseRepository;
use App\Repositories\IncomeRepository;

class UserController extends Controller
{
    /**
     * The logged in user role.
     * 
     * @var role 
     */
    protected $role;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		if(isset(\Auth::user()->role))
			$this->role = \Auth::user()->role;
    }
    
    /**
     * Displays all the expenses of the logged in user.
     *
     * @param Request $request
     * @return Response
     */
    public function changePassword(Request $request, Expense $expense)
    {
        $message = array();
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'old_password' => 'required',
                'password' => 'min:6|required|confirmed',
                'password_confirmation' => 'min:6|required',
            ]);
            if (Hash::check($request->old_password, \Auth::user()->password)) {
                $user = User::find(\Auth::user()->id);
                $user->password = bcrypt($request->password);
                if ($user->save()) {
                    \Auth::user()->password = $user->password;
                }
                $message['class'] = 'alert-success';
                $message['message'] = ' <strong>Successfully!</strong> updated the password.';
            } else {
                $message['class'] = 'alert-danger';
                $message['message'] = ' <strong>Error!</strong> Old-password doesn\'t match.';
            }
        }
        return view('users/changepassword', ['message' => $message ]);
    }
}
