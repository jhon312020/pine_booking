<?php
namespace App\Repositories;

use App\User;
use App\Expense;
use DB;

class ExpenseRepository
{
    /**
     * Get all expenses for the logged in user.
     *
     * @param User $user
     * @return Collection
     */
    public function forUser(User $user) {
        return $user->expenses()
                    ->orderBy('date_of_expense', 'desc')
                    ->get();
    }
    
    /**
     * Get all expenses of the company
     * category wise.
     *
     * @param User $user
     * @return Collection
     */
    public function categoryList(Expense $expense, $expense_date_from, $expense_date_to) {
        return $expense
                    ->where('date_of_expense', '>=', $expense_date_from)
                    ->where('date_of_expense', '<=', $expense_date_to)
                    ->orderBy('date_of_expense', 'desc')
                    ->get();
    }
    
    /**
     * Get all expenses of the company.
     *
     * @param User $user
     * @return Collection
     */
    public function allExpenses(Expense $expense, $expense_date_from, $expense_date_to) {
		$payments = DB::table('employee_payments')->select('employee_payments.id', DB::raw('DATE(employee_payments.updated_at)'), 'employee_payments.category', DB::raw('employees.first_name as type'), 'employee_payments.amount', DB::raw('"" as notes'))
			->leftjoin('employees', 'employee_payments.employee_id', '=', 'employees.id')
			->whereDate(DB::raw('DATE(employee_payments.updated_at)'), '>=', $expense_date_from)
            ->whereDate(DB::raw('DATE(employee_payments.updated_at)'), '<=', $expense_date_to);
			
        return $expense->select('id', 'date_of_expense', 'category', 'name', 'amount', 'notes')
					->union($payments)
					->whereDate('date_of_expense', '>=', $expense_date_from)
                    ->whereDate('date_of_expense', '<=', $expense_date_to)
                    ->orderBy('date_of_expense', 'desc')
                    ->get();
    }
    
    /**
     * Get all expenses of the company in user
     * for the given date.
     *
     * @param User $user
     * @param User $date
     * @return Collection
     */
    public function expensesOfCompany(Expense $expense, $from_date, $to_date) {
        return $expense->where('date_of_expense', '>=' ,$from_date)
                    ->where('date_of_expense', '<=' ,$to_date)
                    ->orderBy('date_of_expense', 'desc')
                    ->get();
    }
    
    /**
     * Get all expenses for the company
     * for the given date.
     *
     * @param User $user
     * @param User $date
     * @return Collection
     */
    public function expensesOfCompanyGroupedByDate(Expense $expense, $month) {
		
		$group_payments = DB::raw('(select sum(amount) as expense_amount, DATE(updated_at) as report_date from employee_payments where MONTH(updated_at) = "'.$month.'" group by DATE(updated_at)) a');
		
		$group_expenses = DB::raw('(select sum(amount) as expense_amount, DATE(date_of_expense) as report_date from expenses where MONTH(date_of_expense) = "'.$month.'" group by DATE(date_of_expense)) b');
		
		$first = DB::table($group_payments)->select(DB::raw('DAY(a.report_date) as report_date'), 'a.expense_amount as totalamount')
					->leftjoin($group_expenses, 'b.report_date', '=', 'a.report_date')
					->whereNull('b.report_date');
					
		$incomes = DB::table($group_expenses)->select(DB::raw('DAY(b.report_date) as report_date'), DB::raw('b.expense_amount + ifnull((select sum(employee_payments.amount) from employee_payments where DATE(employee_payments.updated_at) = DATE(b.report_date)),0) as totalamount'))
					->union($first)
					->orderBy('report_date', 'asc')
					->get();
					
		return json_decode(json_encode($incomes), true);
    }    
    /**
     * Get all expenses of the company
     * for the given all the months in the year.
     *
     * @param User $user
     * @param User $date
     * @return Collection
     */
    public function expensesOfCompanyGroupedByMonth(Expense $expense, $year) {
		
		$group_payments = DB::raw('(select sum(amount) as expense_amount, MONTH(updated_at) as report_month from employee_payments where year(updated_at) = "'.$year.'" group by MONTH(updated_at)) a');
		
		$group_expenses = DB::raw('(select sum(amount) as expense_amount, MONTH(date_of_expense) as report_month from expenses where year(date_of_expense) = "'.$year.'" group by MONTH(date_of_expense)) b');
		
		$first = DB::table($group_payments)->select(DB::raw('a.report_month as report_month'), 'a.expense_amount as totalamount')
					->leftjoin($group_expenses, 'b.report_month', '=', 'a.report_month')
					->whereNull('b.report_month');
					
		$incomes = DB::table($group_expenses)->select(DB::raw('b.report_month as report_month'), DB::raw('b.expense_amount + ifnull((select sum(employee_payments.amount) from employee_payments where MONTH(employee_payments.updated_at) = b.report_month),0) as totalamount'))
					->union($first)
					->orderBy('report_month', 'asc')
					->get();
		
		return json_decode(json_encode($incomes), true);			
    }
}
