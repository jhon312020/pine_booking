<?php

namespace App\Policies;

use App\User;
use App\Expense;

use Illuminate\Auth\Access\HandlesAuthorization;

class ExpensePolicy
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
     * Validates the expense belongs to the logged in
     * user before allowing user to delete it.
     * 
     * @param User $user
     * @param Expense $expense
     * 
     * @return bool
     */
    public function destroy(User $user, Expense $expense)
    {
        return $user->role === 'admin';
    }
}
