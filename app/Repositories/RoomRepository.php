<?php
namespace App\Repositories;

use App\User;
use App\Room;

class RoomRepository
{
    /**
     * Get all expenses for the logged in user.
     *
     * @param User $user
     * @return Collection
     */
    public function forUser(User $user) {
        return $user->rooms()
                    ->get();
    }
    
    /**
     * Get all expenses of the company
     * category wise.
     *
     * @param User $user
     * @return Collection
     */
    public function categoryList(Room $room) {
        return $room->get();
    }
    
    /**
     * Get all expenses of the company.
     *
     * @param User $user
     * @return Collection
     */
    public function allRooms(Room $room) {
        return $room->get();
    }
    

}
