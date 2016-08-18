<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Expense;

use App\Http\Controllers\controller;
use App\Repositories\ExpenseRepository;

class AjaxController extends Controller
{
    /**
     * The expense repository instance.
     * 
     * @var ExpenseRespository 
     */
    protected $expenses;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ExpenseRepository $expenses)
    {
        $this->middleware('auth');
        $this->expenses = $expenses;
    }
    
    
    /**
     * Allows the logged in user to delete his expense.
     *
     * @param Request $request
     * @param Expense $expense
     * @return void
     */
    public function delete(Request $request, Expense $expense)
    {
        if ($this->authorize('destroy', $expense)) {
            Session::put('key', 'value');
            $expense->delete();
        }
        //return redirect('/expense/list');
    }
}
