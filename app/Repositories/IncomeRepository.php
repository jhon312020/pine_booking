<?php
namespace App\Repositories;

use App\User;

class IncomeRepository
{
    /**
     * Get all Incomes for the logged in user.
     *
     * @param User $user
     * @return Collection
     */
    public function forUser(User $user, $date_income_from, $date_income_to) {
        return $user->Incomes()
                    ->where('date_of_income', '>=', $date_income_from)
                    ->where('date_of_income', '<=', $date_income_to)
                    ->orderBy('date_of_income', 'desc')
                    ->get();
    }
    
    /**
     * Get all Incomes for the logged in user
     * for the given date.
     *
     * @param User $user
     * @param User $date
     * @return Collection
     */
    public function incomesOfUser(User $user, $date) {
        return $user->Incomes()
                    ->where('date_of_income', $date)
                    ->orderBy('date_of_income', 'desc')
                    ->get();
    }
    
    /**
     * Get all Incomes for the logged in user
     * for the given date.
     *
     * @param User $user
     * @param User $date
     * @return Collection
     */
    public function incomesOfUserGroupedByDate(User $user, $month) {
        return $user->Incomes()
                    ->select(\DB::raw('day(date_of_income) as day'), \DB::RAW('sum(amount) as totalamount'))
                    ->where(\DB::raw('month(date_of_income)'), '=', $month)
                    ->groupBy('date_of_Income')
                    ->get();
    }
    
    /**
     * Get all Incomes for the logged in user
     * for the given all the months in the year.
     *
     * @param User $user
     * @param User $date
     * @return Collection
     */
    public function incomeOfUserGroupedByMonth(User $user, $year) {
        
        return $user->Incomes()
                    ->select(\DB::raw('month(date_of_income) as month'), \DB::RAW('sum(amount) as totalamount'))
                    ->where(\DB::raw('year(date_of_income)'), '=', $year)
                    ->groupBy(\DB::raw('month(date_of_income)'))
                    ->get();
    }

    /**
     * Get all Incomes for the logged in user
     * for the given month in the year.
     *
     * @param User $user
     * @param User $date
     * @return Collection
     */
    public function incomeOfUserForMonth(User $user, $month, $year) {
        return $user->Incomes()
                    ->select(\DB::raw('month(date_of_income) as month'), \DB::RAW('sum(amount) as totalamount'))
                    ->where(\DB::raw('year(date_of_income)'), '=', "$year")
                    ->where(\DB::raw('month(date_of_income)'), '=', "$month")
                    ->groupBy(\DB::raw('month(date_of_income)'))
                    ->get();
    }
}
