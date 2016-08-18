<?php

namespace App\Policies;

use App\User;
use App\Income;

use Illuminate\Auth\Access\HandlesAuthorization;

class IncomePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    /**
     * Validates the income belongs to the logged in
     * user before allowing user to delete it.
     * 
     * @param User $user
     * @param Income $income
     * 
     * @return bool
     */
    public function destroy(User $user, Income $income)
    {
        return $user->id === $income->user_id;
    }
}
